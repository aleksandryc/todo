<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class UserFormController extends Controller
{
    protected function getFormConfig($formKey)
    {
        $forms = [
            'new-employee' => [
                'title' => 'Add New Employee Form',
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => 'Full name',
                        'type' => 'text',
                        'rules' => 'required|max:255',
                        'placeholder' => 'Enter Full name',
                    ],
                    [
                        'name' => 'shift',
                        'label' => 'Shift',
                        'type' => 'Text', //Needed list of options
                        'rules' => 'required',
                    ],
                    [
                        'name' => 'phone',
                        'label' => 'Phone',
                        'type' => 'tel',
                        // Set the rules
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Active',
                        'type' => 'text', // Needed checkbox
                        // Set the rules
                    ],
                    [
                        'name' => 'supervisor',
                        'label' => 'Supervisor',
                        'type' => 'text',
                        'rules' => 'required|max:255',
                        'placeholder' => 'Enter Full name of supervisor',
                    ],
                    [
                        'name' => 'photo',
                        'label' => 'photo',
                        'type' => 'file',
                        'rules' => 'nullable|file|mimes:jpg, jpeg|max:2048',
                    ],
                ],
            ],
            'new-course' => [
                'title' => 'Add New Course Form',
                'fields' => [
                    [
                        'name' => 'name',
                        'label' => 'Course name',
                        'type' => 'text',
                        'rules' => 'required|max:255',
                        'placeholder' => 'Enter Full name',
                    ],
                    [
                        'name' => 'date',
                        'label' => 'Hard Expiry Date',
                        'type' => 'date',
                        'rules' => 'required',
                    ],
                    [
                        'name' => 'valid',
                        'label' => 'Default Valid Period',
                        'type' => 'date', //Four fields need to be added for chosing period (Years, Month, Weeks, Days)
                    ],
                    [
                        'name' => 'type',
                        'label' => 'Type of course',
                        'type' => 'text', // Options for choose (can choose only one)
                        'rules' => 'required',
                    ],
                ],
            ],
        ];
        return $forms[$formKey] ?? null;
    }
    public function create($formKey)
    {
        $formConfig = $this->getFormConfig($formKey);

        if (!$formConfig) {
            abort(404, 'Form not found');
        }

        return view('forms.create', [
            'formKey' => $formKey,
            'formTitle' => $formConfig['title'],
            'formFields' => $formConfig['fields'],
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
        foreach ($formConfig['fields'] as $field) {
            $rules[$field['name']] = $field['rules'];
        }
        $validateData = $request->validate($rules);
        // Preparation data for PDF
        $pdfData = [
            'title' => $formConfig['title'],
            'fields' => $validateData,
        ];
        // Generate PDF
        $pdf = Pdf::loadView('forms.pdf', $pdfData);

        // Save PDF
        $pdfPath = ('forms/'.'_'.now()->timestamp.'.pdf');
        Storage::put($pdfPath, $pdf->output());

        return redirect()->route('forms.show', $formKey)->with('message', 'Form submitted successfully!');
    }
}
