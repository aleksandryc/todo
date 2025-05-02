<?php

namespace App\Http\Controllers;

use App\Mail\FormSubmissionMail;
use App\Models\SubmittedForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isArray;

class UserFormController extends Controller
{
    protected function getFormConfig($formKey)
    {
        /*
        For testing, form have been added to the controller, but if necessary,
        they can be moved to a separate file or use a database.
        */
        return match ($formKey) {
            'external-access-request' => [
                'title' => 'External Access Request',
                'description' => 'To request an employee be granted external access to company information when outside of Elias Woodwork’s facilities please fill in the following and submit to HR for approval. If a mobile phone or laptop is required, please note that in the devices section. ',
                'fields' => [
                    'supervisor' => [
                        'label' => 'Supervisor',
                        'type' => 'text',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full name',
                        'required' => true,
                    ],
                    'employee' => [
                        'label' => 'Employee',
                        'type' => 'text',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full name',
                        'required' => true,
                    ],
                    'access-type' => [
                        'label' => 'Type of Access (Check all that apply) ',
                        'type' => 'checkbox-group',
                        'options' => ['External Email Access', 'External VPN Access',],
                        'required' => true,
                    ],
                    'device-used' => [
                        'label' => 'Devices being used (MFA requires mobile phone): ',
                        'type' => 'text',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'MFA requires mobile phone',
                        'required' => true,
                    ],
                    'date-range-start' => [
                        'label' => 'Timeframe of approval (Provide start and end dates or “Permanent”) ',
                        'type' => 'date',
                        'rules' => ['nullable', 'date'],

                    ],
                    'date-range-end' => [
                        'label' => 'Timeframe of approval (Provide start and end dates or “Permanent”) ',
                        'type' => 'date',
                        'rules' => ['nullable', 'date', 'after_or_equal:date-range-start'],
                        'required' => false,
                    ],
                    'date-range-perm' => [
                        'label' => 'Timeframe of approval (Provide start and end dates or “Permanent”) ',
                        'type' => 'checkbox',
                        'options' => '“Permanent”',
                        'rules' => ['boolean', ],
                        'required' => false,
                    ],
                    'reason' => [
                        'label' => 'Reason (Provide a brief description of why this is needed)',
                        'type' => 'textarea',
                        'required' => true,
                    ]
                ],
            ],
            'external-access' => [
                'title' => 'External Access Request',
                'description' => 'Use this form to request external access.',
                'sections' => [

                    // Simple text field
                    [
                        'type' => 'field',
                        'name' => 'employee_name',
                        'label' => 'Employee Name',
                        'input_type' => 'text',
                        'rules' => ['required', 'string', 'max:255'],
                    ],

                    // Checkbox group
                    [
                        'type' => 'group',
                        'label' => 'Access Type',
                        'name' => 'access_type',
                        'input_type' => 'checkbox-group',
                        'options' => ['VPN', 'Email', 'Remote Desktop'],
                        'rules' => ['required', 'array'],
                    ],

                    // Группа с условием (перманентный доступ)
                    [
                        'type' => 'group',
                        'label' => 'Access Duration',
                        'name' => 'access_duration',
                        'children' => [
                            [
                                'type' => 'field',
                                'name' => 'permanent_access',
                                'label' => 'Permanent',
                                'input_type' => 'checkbox',
                                'rules' => ['nullable', 'boolean'],
                            ],
                            [
                                'type' => 'field',
                                'name' => 'date_start',
                                'label' => 'Start Date',
                                'input_type' => 'date',
                                'rules' => ['nullable', 'date'],
                                'show_if' => ['permanent_access' => false],
                            ],
                            [
                                'type' => 'field',
                                'name' => 'date_end',
                                'label' => 'End Date',
                                'input_type' => 'date',
                                'rules' => ['nullable', 'date', 'after_or_equal:date_start'],
                                'show_if' => ['permanent_access' => false],
                            ],
                        ],
                    ],

                    // Селект
                    [
                        'type' => 'field',
                        'name' => 'access_level',
                        'label' => 'Access Level',
                        'input_type' => 'select',
                        'options' => ['Basic', 'Admin', 'Full'],
                        'rules' => ['required', 'in:Basic,Admin,Full'],
                    ],

                    // Загрузка файлов
                    [
                        'type' => 'field',
                        'name' => 'supporting_documents',
                        'label' => 'Upload Supporting Documents',
                        'input_type' => 'file',
                        'rules' => ['nullable', 'file', 'max:5120'],
                    ],
                ],
            ],
            'new-employee' => [
                'title' => 'Add New Employee Form',
                'description' => 'Use this form to request external access.',
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
        };
    }

    protected function rulesValidation($data)
    {
        /* Received a form configuration and add rules by default if not specified.
        */
        $rules = [];
        foreach ($data['fields'] as $name => $field) {
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
        return $rules;
    }

    public function show($formKey)
    {
        // Get form name from url
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
        // Retrive for configuration
        $formConfig = $this->getFormConfig($formKey);
        if (!$formConfig) {
            abort(404, 'Form Not Found');
        }
        // Validation
        $rules = $this->rulesValidation($formConfig);
        // Geting form data after validation
        $formData = $request->validate($rules);
        dd($formData);
    }
}
