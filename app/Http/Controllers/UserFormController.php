<?php

namespace App\Http\Controllers;

use App\Mail\UserForms\FormSubmissionMail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Mail;
use Storage;
use Str;

class UserFormController extends Controller
{
    protected function getFormConfig($formKey)
    {
        /*
        For example, few forms have been added to the controller, but if necessary,
        they can be moved to a separate file or use a database.
        */
        return match ($formKey) {
            'external-access-request' => [
                'title' => 'External Access Request',
                'description' => 'To request an employee be granted external access to company information when outside of Elias Woodwork’s facilities please fill in the following and submit to HR for approval. If a mobile phone or laptop is required, please note that in the devices section. ',
                'fields' => [
                    'names-group' => [
                        'supervisor' => [
                            'name' => 'supervisor',
                            'label' => 'Supervisor',
                            'type' => 'text',
                            'rules' => ['required', 'max:255'],
                            'placeholder' => 'Enter Full name',
                            'required' => true,
                        ],
                        'employee' => [
                            'name' => 'employee',
                            'label' => 'Employee',
                            'type' => 'text',
                            'rules' => ['required', 'max:255'],
                            'placeholder' => 'Enter Full name',
                            'required' => true,
                        ],
                    ],
                    'access-type' => [
                        'name' => 'access-type',
                        'label' => 'Type of Access (Check all that apply) ',
                        'type' => 'checkbox-group',
                        'options' => ['External Email Access', 'External VPN Access',],
                        'required' => true,
                    ],
                    [
                        'External Email Access' =>
                        [
                            'label' => 'Devices being used (MFA requires mobile phone): ',
                            'type' => 'text',
                            'rules' => ['required', 'max:255'],
                            'placeholder' => 'MFA requires mobile phone',
                            'required' => true,
                        ],
                        'External VPN Access' =>
                        [
                            'label' => 'Devices being used: ',
                            'type' => 'text',
                            'rules' => ['required', 'max:255'],
                            'placeholder' => 'MFA requires mobile phone',
                            'required' => true,
                        ]
                    ],
                    'date-range-start' => [
                        'label' => 'Timeframe of approval (Provide start and end dates or “Permanent”) ',
                        'type' => 'date',
                        'rules' => ['nullable', 'date'],
                        'conditional-rules' => [
                            'when' => [
                                'field' => 'date-range-perm',
                                'value' => false,
                                'rules' => ['required', 'date'],
                            ],
                        ],

                    ],
                    'date-range-end' => [
                        'label' => 'Timeframe of approval (Provide start and end dates or “Permanent”) ',
                        'type' => 'date',
                        'rules' => ['nullable', 'date', 'after_or_equal:date-range-start'],
                        'required' => false,
                        'conditional-rules' => [
                            'when' => [
                                'field' => 'date-range-perm',
                                'value' => false,
                                'rules' => ['required', 'date'],
                            ],
                        ],
                    ],
                    'date-range-perm' => [
                        'label' => 'Timeframe of approval (Provide start and end dates or “Permanent”) ',
                        'type' => 'checkbox',
                        'options' => '“Permanent”',
                        'rules' => ['boolean',],
                        'required' => false,
                    ],
                    'reason' => [
                        'label' => 'Reason (Provide a brief description of why this is needed)',
                        'type' => 'textarea',
                        'required' => true,
                    ]
                ],
            ],
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
                    'logo' => [
                        'label' => 'Attach a file',
                        'type' => 'file',
                        'rules' => ['nullable', 'file', 'max:2048'],
                        'required' => false,
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
    protected function extractFieldsWithType($formConfig)
    {
        $result = [];

        //Ckeck for key 'type' in array
        if (isset($formConfig['fields']) && is_array($formConfig['fields'])) {
            $result = $this->extractFieldsRecursively($formConfig['fields']);
        }

        foreach ($formConfig as $key => $value) {
            if (is_array($value) && $key !== 'fields') {
                $result = array_merge($result, $this->extractFieldsWithType($value));
            }
        }
        return $result;
    }

    protected function extractFieldsRecursively($fields)
    {
        $result = [];

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                if (isset($value['type'])) {
                    $result[$key] = $value;
                }
                $result = array_merge($result, $this->extractFieldsRecursively($value));
            }
        }

        return $result;
    }
    public function show($formKey)
    {
        // Get form name from url
        $formConfig = $this->getFormConfig($formKey);

        if (!$formConfig) {
            abort(404, 'Form not found');
        };
        dump($this->extractFieldsWithType($formConfig));
        return view('forms.show', [
            'formKey' => $formKey,
            'formConfig' => $formConfig,
            'formComponents' => $this->extractFieldsWithType($formConfig),
        ]);
    }

    protected function validateRules ($formData, $formConfig)
    {
        $rules = [];
        foreach ($formConfig['rules'] as $name => $field) {
            $fieldRules = [];
            if (!empty($field['rules'])) {
                $fieldRules = $field['rules'];
            } else {
                //Automated generated rules, if 'rules' dose not exist
                switch ($field['type']) {
                    case 'radio':
                        $fieldRules = ['nullable', 'boolean'];
                        break;
                    case 'select':
                        $fieldRules = [
                            $field['required'] ? 'required' : 'nullable',
                            Rule::in($field['options']),
                        ];
                        break;
                    case 'email':
                        $fieldRules = [$field['required'] ? 'required' : 'nullable', 'email'];
                        break;
                    case 'file':
                        $fieldRules = isset($field['required']) && $field['required']
                        ? ['required', 'string']
                        : ['nullable', 'file', 'max:5120'];
                        break;
                    case 'checkbox':
                        $fieldRules = ['nullable', 'boolean'];
                        break;
                    case 'checkbox-group':
                        $fieldRules = ['nullable', 'array'];
                        break;
                    case 'date':
                        $fieldRules = $field['require'] ?? false ? ['required', 'date'] : ['nullable', 'date'];
                        ;
                        break;
                    default:
                        $fieldRules = $field['required'] ? ['required', 'string'] : ['nullable', 'string'];
                        break;
                }
            }
            //Add conditional rules if applicable
            if (isset($field['conditional-rules']['when'])) {
                $condition = $field['conditional-rules']['when'];
                $dependentField = $condition['field'];
                $dependentValue = $condition['value'];
                $actualValue = isset($formData[$dependentField]) ? $formData[$dependentField] : null;

                //Apply conditional rules
                if ($actualValue === $dependentValue) {
                    $fieldRules = array_merge($fieldRules, $condition['rules']);
                }
            }

            //Delet duplicates
            $fieldRules = array_unique($fieldRules, SORT_REGULAR);

            //Add rule for field
            $rules[$name] = $fieldRules;
        }
        return $rules;
    }
    public function submit(Request $request, $formKey)
    {
        $formConfig = $this->extractFieldsWithType($this->getFormConfig($formKey));
        if (!$formConfig) {
            abort(404, 'Form Not Found');
        }
        dd($request->post(), $formConfig);
        // Validation
        $rules = [];
        foreach ($formConfig['fields'] as $name => $field) {
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
                case 'checkbox':
                    $rules[$name] = ['nullable'];
                    break;
                case 'checkbox-group':
                    $rules[$name] = ['nullable', 'array'];
                    break;
                default:
                    $rules[$name] = $field['required'] ? ['required', 'string'] : ['nullable', 'string'];
                    break;
            }
        }
        $formData = $request->validate($rules);
        $embeddedImages = [];
        $attachments = [];

        // Date interval in one field
        if (isset($formData['date-range-start']) && isset($formData['date-range-end'])) {
            $formData['date-range'] = 'From ' . $formData['date-range-start'] .  ' to ' . $formData['date-range-end'];
            unset($formData['date-range-start'], $formData['date-range-end']);
        }

        // Save upload file
        foreach ($formConfig['fields'] as $name => $field) {
            if ($field['type'] === 'checkbox' || $field['type'] === 'checkbox-group' && empty($formData[$name])) {
                unset($formData[$name]);
            }
            if ($field['type'] === 'file' && $request->hasFile($name)) {
                $uploadedfile = $request->file($name);
                $filepath = $uploadedfile->store('attachments', 'public');
                $formData[$name] = $filepath; //Adding file path to form

                $fullPath = storage_path('app/public/' . $filepath);
                $attachments[] = $fullPath;

                //Prepare embedded image
                if (Str::startsWith(File::mimeType($fullPath), 'image/')) {
                    $mimeType = File::mimeType($fullPath);
                    $base64 = 'data:' . $mimeType . ';base64,' . base64_encode(File::get($fullPath));
                    $embeddedImages[$name] = $base64;
                }
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
        /* Need to create table and model,
         This code saved json string in db
         SubmittedForm::create([
            'form_name' => $formConfig['title'] ?? 'Untitled Form',
            'form_json' => $formData,
        ]);
        */

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
        Mail::to('admin@example.com')->send(new FormSubmissionMail($pdfData, $attachmentPath));
        // Show pdf in browser
        return $pdf->stream('form_submission.pdf');
    }
}
