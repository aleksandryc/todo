<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

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
                        'rules' => ['nullable', 'file', 'mimes:jpg', 'jpeg', 'max:2048'],
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

        // Save upload file
        /*
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads/forms', 'public');
            $formData['file_path'] = $path;
        }
        */
        $validateData = $request->validate($rules);
        // Preparation data for PDF
        $pdfData = [
            'title' => $formConfig['title'],
            'fields' => $validateData,
        ];
        // Generate PDF
        $pdf = Pdf::loadView('forms.pdf', $pdfData);

        // Show pdf in browser
        return $pdf->stream('form_submission.pdf');
    }
}
