<?php

namespace App\Http\Controllers;

use App\Models\SubmittedForm;

use App\Mail\FormSubmissionMail;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Barryvdh\DomPDF\Facade\Pdf;

class UserFormController extends Controller
{
    protected function formatField($value)
    {
        $value = e($value);
        $value = preg_replace(
            '/((https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-._~:/?#[@!$&\'()*+,;=]*)?)/i',
            '<a href="$1" target="_blank">$1</a>',
            $value
        );
        $value = preg_replace(
            '/([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i',
            '<a href="mailto:$1">$1</a>',
            $value
        );

        $value = preg_replace(
            '/(\+?\d[\d\s\-]{7,})/',
            '<a href="tel:$1">$1</a>',
            $value
        );
        return $value;
    }
    protected function generateValidationRules(array $fields)
    {
        $rules = [];
        foreach ($fields['fields'] as $name => $field) {
            if (!empty($field['rules'])) {
                $rules[$name] = $field['rules'];
                continue;
            }
            //Automated generated rules, if 'rules' dose not exist
            switch ($field['type']) {
                case 'radio':
                case 'select':
                    $rules[$name] = [
                        $field['required'] ? 'required' : 'nullable',
                        Rule::in($field['options']),
                    ];
                    break;
                case 'email':
                    $rules[$name] = [$field['required'] ? 'required' : 'nullable', 'email'];
                    break;
                case 'file':
                    $rules[$name] = $rules[$name] = isset($field['required']) && $field['required']
                    ? ['required', 'string']
                    : ['nullable', 'file', 'max:5120'];
                    break;
                default:
                    $rules[$name] = $field['required'] ? ['required', 'string'] : ['nullable', 'string'];
                    break;
            }
        }
        return $rules;
    }
    protected function getFormConfig($formKey)
    {
        /*
        For example, two forms have been added to the controller, but if necessary,
        they can be moved to a separate file or use a database.
        */
        return match ($formKey) {
            'new-employee' => [
                'title' => 'Add New Employee Form',
                'fields' => [
                    'name' => [
                        'label' => 'Full name',
                        'type' => 'text',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full name',
                    ],
                    'shift' => [
                        'label' => 'Shift',
                        'type' => 'select',
                        'options' => ['Morden Day', 'Morden Evening', 'Office', 'Winkler Day', 'Winkler Evening', 'Driver', 'Cleaning'],
                        'rules' => ['required'],
                        'required' => true,
                    ],
                    'phone' => [
                        'label' => 'Phone',
                        'type' => 'tel',
                        'rules' => ['required', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
                        'placeholder' => 'Enter phone number',
                    ],
                    'status' => [
                        'label' => 'Active',
                        'type' => 'radio',
                        'options' => ['Yes', 'No'],
                        'rules' => ['required'],
                    ],
                    'supervisor' => [
                        'label' => 'Supervisor',
                        'type' => 'text',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full name of supervisor',
                    ],
                    'file' => [
                        'label' => 'Photo',
                        'type' => 'file',
                        'rules' => ['nullable', 'file', 'max:2048'],
                    ],
                ],
            ],
            'new-course' => [
                'title' => 'Add New Course Form',
                'fields' => [
                    'email' => [
                        'label' => 'Email',
                        'type' => 'email',
                        'rules' => ['required', 'email', 'regex: /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/'],
                        'placeholder' => 'Enter email',
                    ],
                    'name' => [
                        'label' => 'Course name',
                        'type' => 'textarea',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full course name',
                    ],

                    'date' => [
                        'label' => 'Hard Expiry Date',
                        'type' => 'date',
                        'rules' => ['required'],
                    ],

                    'valid' => [
                        'label' => 'Default Valid Period',
                        'type' => 'date', //TODO Four fields need to be added for chosing period (Years, Month, Weeks, Days)
                        'rules' => ['nullable'],
                    ],

                    'type' => [
                        'label' => 'Type of course',
                        'type' => 'select',
                        'options' => ['Policies', 'Toolbox Talks', 'Manitoba SAFE Work Courses'],
                        'rules' => ['required'],
                        'required' => true,
                    ],
                    'website' => [
                        'label' => 'Link to website',
                        'type' => 'url',
                        'rules' => ['nullable', 'url'],
                        'required' => false,
                        'placeholder' => 'Example: https://example.com',
                    ],
                    'attachment' => [
                        'label' => 'Attach a file',
                        'type' => 'file',
                        'rules' => ['nullable', 'file', 'max:2048'],
                        'required' => false,
                    ],
                ],
            ],
            default => abort(404),
        };
    }
    public function show($formKey)
    {
        // Get for from url
        $formConfig = $this->getFormConfig($formKey);

        if (!$formConfig) {
            abort(404, 'Form not found');
        }

        return view('forms.show', [
            'formKey' => $formKey,
            'formConfig' => $formConfig,
        ]);
    }
    public function submit(Request $request, $formKey)
    {
        $formConfig = $this->getFormConfig($formKey);
        if (!$formConfig) {
            abort(404, 'Form Not Found');
        }
        $rules = $this->generateValidationRules($formConfig);
        $formData = $request->validate($rules);
        $embeddedImages = []; // To show in pdf
        $attachments = []; //Attach to mail
        // Save upload file
        foreach ($formConfig['fields'] as $name => $field) {
            if ($field['type'] === 'file' && $request->hasFile($name)) {
                $uploadedfile = $request->file($name);
                $filepath = $uploadedfile->store('attachments', 'public');
                $fullPath = storage_path('app/public/' . $filepath);
                $mimeType = File::mimeType($fullPath);
                $fileSize = File::size($fullPath);

                $formData[$name] = $filepath; //Adding file path to form

                //Prepare embedded image
                if (Str::startsWith(File::mimeType($fullPath), 'image/')) {
                    if ($fileSize <= 5 * 1024 * 1024) {
                        $embeddedImages[$name] = 'data:' . $mimeType . ';base64,' . base64_encode(File::get($fullPath));
                        $attachments = $fullPath;
                    } else {
                        $formData[$name] = Storage::url($filepath); //Link for download
                    }
                } else {
                    if ($fileSize <= 5 * 1024 * 1024) {
                        $attachments = $fullPath;
                    } else {
                        $formData[$name] = Storage::url($filepath); //Link for download
                    }
                }
            } else {
                $formData[$name] = $this->formatField($field);
            }
        }

        //Stoere in JSON
        $jsonPath = storage_path('app/public/forms/');
        $formDataWithName = [
            'form_name' => $formConfig['title'] ?? 'untitled',
            'submitted_at' => now()->toDayDateTimeString(),
            'fields' => $formData
        ];
        if (!File::exists($jsonPath)) {
            File::makeDirectory($jsonPath, 0755, true);
        }
        $fileName = 'form_' . now()->format('Ymd_His') . '.json';
        File::put($jsonPath . $fileName, json_encode($formDataWithName, JSON_PRETTY_PRINT));

        //store in db
        SubmittedForm::create([
            'form_name' => $formConfig['title'] ?? 'Untitled Form',
            'form_json' => $formData,
        ]);

        // Preparation data for PDF
        $pdfData = [
            'title' => $formConfig['title'],
            'fields' => $formData,
            'embeddedImages' => $embeddedImages,
        ];
        // Generate PDF
        $pdf = Pdf::loadView('forms.pdf', $pdfData);
        $pdf->setOptions(['isRemoteEnabled' => true]);

        // Save PDF in file
        $pdfContent = $pdf->output();
        $relativePath = 'pdf/form_' . now()->format('Ymd_His') . '.pdf';
        Storage::disk('public')->put($relativePath, $pdfContent);
        $attachmentPath = 'app/public/' . $relativePath;
        Mail::to('admin@example.com')->send(new FormSubmissionMail($pdfContent, $attachments, $formData, $embeddedImages));
        // Show pdf in browser
        return $pdf->stream('form_submission.pdf');
    }
}
