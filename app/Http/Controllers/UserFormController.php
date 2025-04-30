<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\SubmittedForm;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserFormController extends Controller
{
    protected function getFormConfig($formKey)
    {
        /*
        For example, two forms have been added to the controller, but if necessary,
        they can be moved to a separate file or use a database.
        */
        return match($formKey) {
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
        // Validation
        $rules = [];
        foreach ($formConfig['fields'] as $name => $field) {
            if(!empty($field['rules'])) {
                $rules[$name] = $field['rules'];
                continue;
            }
            //Automated generated rules, if 'rules' dose not exist
            switch ($field['type']) {
                case 'radio':
                case 'select':
                    $rules[$name] = [$field['required'] ? 'required' : 'nullable',
                    Rule::in($field['options']),
                ];
                    break;
                case 'email':
                    $rules[$name] = [$field['required'] ? 'required' : 'nullable', 'email'];
                    break;
                case 'file':
                    $rules[$name] = [$field['required'] ? 'required' : 'nullable', 'file'];
                    break;
                default:
                    $rules[$name] = $field['required'] ? ['required', 'string'] : ['nullable', 'string'];
                    break;
            }
        }

        $formData = $request->validate($rules);
        // Save upload file
        foreach ($formConfig['fields'] as $name => $field){
            if ($field['type'] === 'file' && $request->hasFile($name)) {
                $uploadedfile = $request->file($name);
                $filepath = $uploadedfile->store('attachments', 'public');
                $formData[$name] = $filepath; //Adding file path to form

                //Prepare embedded image
                $fullPath = storage_path('app/public/' . $filepath);
                $mimeType = File::mimeType($fullPath);
                $base64 = 'data:' . $mimeType . ':base64.' . base64_encode(File::get($fullPath));
                $formData[$name . '_embedded'] = $base64;

            }
        }

        //Stoere in JSON
        $jsonPath = storage_path('app/forms/json/');
        $formDataWithName = [
            'form_name' => $formConfig['title'] ?? 'untitled',
            'submitted_at' => now()->toDayDateTimeString(),
            'fields' => $formData
        ];


        if (!File::exists($jsonPath)) {
            File::makeDirectory($jsonPath, 0755, true);
        }
        $fileName = 'form_'. now()->format('Ymd_His').'.json';
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
        ];
        // Generate PDF
        $pdf = Pdf::loadView('forms.pdf', $pdfData);

        // Save PDF in file
        $pdfPath = 'forms/pdf/form_' . now()->format('Ymd_His') . '.pdf';
        Storage::disk('local')->put($pdfPath, $pdf->output());

        // Show pdf in browser
        return $pdf->stream('form_submission.pdf');
    }
}
