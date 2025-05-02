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
                        'rules' => ['array',],
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
