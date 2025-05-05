<?php

namespace App\Http\Controllers;

use App\Mail\UserForms\FormSubmissionMail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Mail;
use Storage;

class UserFormController extends Controller
{
    protected function getFormConfig($formKey)
    {
        return match ($formKey) {
            'external-access-request' => [
                'title' => 'External Access Request',
                'description' => 'To request an employee be granted external access to company information when outside of Elias Woodwork’s facilities please fill in the following and submit to HR for approval. If a mobile phone or laptop is required, please note that in the devices section.',
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
                        'label' => 'Type of Access (Check all that apply)',
                        'type' => 'checkbox-group',
                        'options' => ['External Email Access', 'External VPN Access'],
                        'rules' => ['required', 'array', Rule::in(['External Email Access', 'External VPN Access'])],
                        'required' => true,
                    ],
                    'device-used-email' => [
                        'name' => 'device-used-email',
                        'label' => 'Devices being used for Email (MFA requires mobile phone)',
                        'type' => 'text',
                        'rules' => ['nullable', 'max:255'],
                        'placeholder' => 'MFA requires mobile phone',
                        'required' => false,
                        'conditional-rules' => [
                            'when' => [
                                [
                                    'field' => 'access-type',
                                    'value' => 'External Email Access',
                                    'rules' => ['required', 'max:255'],
                                ],
                            ],
                        ],
                    ],
                    'device-used-vpn' => [
                        'name' => 'device-used-vpn',
                        'label' => 'Devices being used for VPN',
                        'type' => 'text',
                        'rules' => ['nullable', 'max:255'],
                        'placeholder' => 'MFA requires mobile phone',
                        'required' => false,
                        'conditional-rules' => [
                            'when' => [
                                [
                                    'field' => 'access-type',
                                    'value' => 'External VPN Access',
                                    'rules' => ['required', 'max:255'],
                                ],
                            ],
                        ],
                    ],
                    'date-range-start' => [
                        'name' => 'date-range-start',
                        'label' => 'Timeframe of approval (Start date)',
                        'type' => 'date',
                        'rules' => ['nullable', 'date'],
                        'conditional-rules' => [
                            'when' => [
                                [
                                    'field' => 'date-range-perm',
                                    'value' => false,
                                    'rules' => ['required', 'date'],
                                ],
                                [
                                    'field' => 'date-range-perm',
                                    'value' => true,
                                    'rules' => ['prohibited'],
                                ],
                            ],
                        ],
                    ],
                    'date-range-end' => [
                        'name' => 'date-range-end',
                        'label' => 'Timeframe of approval (End date)',
                        'type' => 'date',
                        'rules' => ['nullable', 'date', 'after_or_equal:date-range-start'],
                        'required' => false,
                        'conditional-rules' => [
                            'when' => [
                                [
                                    'field' => 'date-range-perm',
                                    'value' => false,
                                    'rules' => ['required', 'date', 'after_or_equal:date-range-start'],
                                ],
                                [
                                    'field' => 'date-range-perm',
                                    'value' => true,
                                    'rules' => ['prohibited'],
                                ],
                            ],
                        ],
                    ],
                    'date-range-perm' => [
                        'name' => 'date-range-perm',
                        'label' => 'Permanent',
                        'type' => 'checkbox',
                        'options' => 'Permanent',
                        'rules' => ['boolean'],
                        'required' => false,
                        'conditional-rules' => [
                            'when' => [
                                [
                                    'field' => 'date-range-start',
                                    'value' => null,
                                    'operator' => '===',
                                    'rules' => [],
                                ],
                                [
                                    'field' => 'date-range-start',
                                    'value' => null,
                                    'operator' => '!==',
                                    'rules' => ['prohibited'],
                                ],
                            ],
                        ],
                    ],
                    'reason' => [
                        'name' => 'reason',
                        'label' => 'Reason (Provide a brief description of why this is needed)',
                        'type' => 'textarea',
                        'rules' => ['required', 'string', 'max:1000'],
                        'required' => true,
                    ],
                ],
            ],
            'new-employee' => [
                'title' => 'Add New Employee Form',
                'fields' => [
                    'name' => [
                        'name' => 'name',
                        'label' => 'Full name',
                        'type' => 'text',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full name',
                        'required' => true,
                    ],
                    'shift' => [
                        'name' => 'shift',
                        'label' => 'Shift',
                        'type' => 'select',
                        'options' => ['Morden Day', 'Morden Evening', 'Office', 'Winkler Day', 'Winkler Evening', 'Driver', 'Cleaning'],
                        'rules' => ['required'],
                        'required' => true,
                    ],
                    'phone' => [
                        'name' => 'phone',
                        'label' => 'Phone',
                        'type' => 'tel',
                        'rules' => ['required', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
                        'placeholder' => 'Enter phone number',
                        'required' => true,
                    ],
                    'status' => [
                        'name' => 'status',
                        'label' => 'Active',
                        'type' => 'radio',
                        'options' => ['Yes', 'No'],
                        'rules' => ['required'],
                        'required' => true,
                    ],
                    'supervisor' => [
                        'name' => 'supervisor',
                        'label' => 'Supervisor',
                        'type' => 'text',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full name of supervisor',
                        'required' => true,
                    ],
                    'file' => [
                        'name' => 'file',
                        'label' => 'Photo',
                        'type' => 'file',
                        'rules' => ['required', 'file', 'mimes:jpg,png', 'max:5120'],
                        'required' => true,
                    ],
                ],
            ],
            'new-course' => [
                'title' => 'Add New Course Form',
                'fields' => [
                    'email' => [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => 'email',
                        'rules' => ['required', 'email', 'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/'],
                        'placeholder' => 'Enter email',
                        'required' => true,
                    ],
                    'name' => [
                        'name' => 'name',
                        'label' => 'Course name',
                        'type' => 'textarea',
                        'rules' => ['required', 'max:255'],
                        'placeholder' => 'Enter Full course name',
                        'required' => true,
                    ],
                    'date' => [
                        'name' => 'date',
                        'label' => 'Hard Expiry Date',
                        'type' => 'date',
                        'rules' => ['required', 'date'],
                        'required' => true,
                    ],
                    'valid' => [
                        'name' => 'valid',
                        'label' => 'Default Valid Period',
                        'type' => 'date',
                        'rules' => ['nullable', 'date'],
                        'required' => false,
                    ],
                    'logo' => [
                        'name' => 'logo',
                        'label' => 'Attach a file',
                        'type' => 'file',
                        'rules' => ['nullable', 'file', 'mimes:jpg,png', 'max:2048'],
                        'required' => false,
                    ],
                    'type' => [
                        'name' => 'type',
                        'label' => 'Type of course',
                        'type' => 'select',
                        'options' => ['Policies', 'Toolbox Talks', 'Manitoba SAFE Work Courses'],
                        'rules' => ['required'],
                        'required' => true,
                    ],
                    'website' => [
                        'name' => 'website',
                        'label' => 'Link to website',
                        'type' => 'url',
                        'rules' => ['nullable', 'url'],
                        'placeholder' => 'Example: https://example.com',
                        'required' => false,
                    ],
                    'attachment' => [
                        'name' => 'attachment',
                        'label' => 'Attach a file',
                        'type' => 'file',
                        'rules' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
                        'required' => false,
                    ],
                ],
            ],
            default => null,
        };
    }

    protected function extractFieldsWithType($formConfig)
    {
        $result = [];

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
                if (isset($value['type']) && isset($value['name'])) {
                    $result[$value['name']] = $value;
                }
                $result = array_merge($result, $this->extractFieldsRecursively($value));
            }
        }

        return $result;
    }

    public function show($formKey)
    {
        $formConfig = $this->getFormConfig($formKey);

        if (!$formConfig) {
            abort(404, 'Form not found');
        }

        return view('forms.show', [
            'formKey' => $formKey,
            'formConfig' => $formConfig,
            'formComponents' => $this->extractFieldsWithType($formConfig),
        ]);
    }

    protected function validateRules($formData, $formConfig)
    {
        $rules = [];

        foreach ($formConfig as $name => $field) {
            $fieldRules = [];

            if (!empty($field['rules'])) {
                $fieldRules = $field['rules'];
            } else {
                switch ($field['type']) {
                    case 'radio':
                        $fieldRules = ['nullable', 'in:' . implode(',', $field['options'])];
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
                            ? ['required', 'file', 'max:5120']
                            : ['nullable', 'file', 'max:5120'];
                        break;
                    case 'checkbox':
                        $fieldRules = ['nullable', 'boolean'];
                        break;
                    case 'checkbox-group':
                        $fieldRules = ['nullable', 'array', Rule::in($field['options'])];
                        break;
                    case 'date':
                        $fieldRules = isset($field['required']) && $field['required']
                            ? ['required', 'date']
                            : ['nullable', 'date'];
                        break;
                    case 'textarea':
                        $fieldRules = isset($field['required']) && $field['required']
                            ? ['required', 'string', 'max:1000']
                            : ['nullable', 'string', 'max:1000'];
                        break;
                    case 'tel':
                        $fieldRules = isset($field['required']) && $field['required']
                            ? ['required', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/']
                            : ['nullable', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'];
                        break;
                    case 'url':
                        $fieldRules = isset($field['required']) && $field['required']
                            ? ['required', 'url']
                            : ['nullable', 'url'];
                        break;
                    default:
                        $fieldRules = isset($field['required']) && $field['required']
                            ? ['required', 'string', 'max:255']
                            : ['nullable', 'string', 'max:255'];
                        break;
                }
            }

            if (isset($field['conditional-rules']['when'])) {
                $conditions = (array) $field['conditional-rules']['when'];
                foreach ($conditions as $condition) {
                    if (!is_array($condition) || !isset($condition['field'], $condition['value'], $condition['rules'])) {
                        \Log::warning("Invalid conditional rule for field {$name}: ", ['condition' => $condition]);
                        continue;
                    }

                    $dependentField = $condition['field'];
                    $dependentValue = $condition['value'];
                    $operator = $condition['operator'] ?? '===';

                    $actualValue = isset($formData[$dependentField]) ? $formData[$dependentField] : null;
                    if (is_array($actualValue)) {
                        $conditionMet = in_array($dependentValue, $actualValue);
                    } else {
                        $conditionMet = match ($operator) {
                            '===' => $actualValue === $dependentValue,
                            '!==' => $actualValue !== $dependentValue,
                            default => $actualValue === $dependentValue,
                        };
                    }

                    if ($conditionMet) {
                        $fieldRules = array_merge($fieldRules, $condition['rules']);
                    }
                }
            }

            $fieldRules = array_unique($fieldRules, SORT_REGULAR);
            $rules[$name] = $fieldRules;
        }

        return $rules;
    }

    public function submit(Request $request, $formKey)
    {
        $fullFormConfig = $this->getFormConfig($formKey);
        if (!$fullFormConfig) {
            abort(404, 'Form Not Found');
        }

        $formComponents = $this->extractFieldsWithType($fullFormConfig);
        $formData = $request->only(array_keys($formComponents));
        $rules = $this->validateRules($formData, $formComponents);

        $validator = Validator::make($formData, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $embeddedImages = [];
        $attachments = [];

        // Объединение дат в одно поле
        if (!empty($validatedData['date-range-start']) && !empty($validatedData['date-range-end'])) {
            $validatedData['date-range'] = 'From ' . $validatedData['date-range-start'] . ' to ' . $validatedData['date-range-end'];
            unset($validatedData['date-range-start'], $validatedData['date-range-end']);
        } elseif (!empty($validatedData['date-range-perm'])) {
            $validatedData['date-range'] = 'Permanent';
            unset($validatedData['date-range-start'], $validatedData['date-range-end']);
        }

        // Обработка файлов
        foreach ($formComponents as $name => $field) {
            if ($field['type'] === 'file' && $request->hasFile($name)) {
                $uploadedFile = $request->file($name);
                $fileName = Str::random(10) . '.' . $uploadedFile->getClientOriginalExtension();
                $filePath = $uploadedFile->storeAs('attachments', $fileName, 'public');
                $validatedData['files'][$name] = $filePath;

                $fullPath = storage_path('app/public/' . $filePath);
                $attachments[] = $fullPath;

                if (Str::startsWith(File::mimeType($fullPath), 'image/')) {
                    $mimeType = File::mimeType($fullPath);
                    $base64 = 'data:' . $mimeType . ';base64,' . base64_encode(File::get($fullPath));
                    $embeddedImages[$name] = $base64;
                }
            }
        }

        // Фильтрация пустых данных
        $filteredData = array_filter($validatedData, function ($value, $key) {
            if ($key === 'files' || $key === 'embedded_images') {
                return true;
            }
            return !is_null($value) && $value !== '' && !(is_array($value) && empty($value));
        }, ARRAY_FILTER_USE_BOTH);

        $filteredData['files'] = $validatedData['files'] ?? [];
        $filteredData['embedded_images'] = $embeddedImages;

        // Сохранение в JSON
        $jsonPath = storage_path('app/public/forms/');
        $formDataWithName = [
            'form_name' => $fullFormConfig['title'] ?? 'Untitled',
            'submitted_at' => now()->toDayDateTimeString(),
            'fields' => $filteredData,
        ];
        if (!File::exists($jsonPath)) {
            File::makeDirectory($jsonPath, 0755, true);
        }
        $fileName = 'form_' . now()->format('Ymd_His') . '.json';
        File::put($jsonPath . $fileName, json_encode($formDataWithName, JSON_PRETTY_PRINT));

        // Подготовка данных для PDF
        $pdfData = [
            'title' => $fullFormConfig['title'],
            'description' => $fullFormConfig['description'],
            'fields' => $filteredData,
            'embedded_images' => $embeddedImages,
            'form_components' => $formComponents,
        ];

        // Генерация PDF
        $pdf = Pdf::loadView('forms.pdf', $pdfData);
        $pdf->setOptions(['isRemoteEnabled' => true]);
        $pdfName = 'form_' . now()->format('Ymd_His') . '.pdf';
        $pdfPath = 'pdf/' . $pdfName;
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // Отправка email
        $emailAttachments = array_merge($attachments, [storage_path('app/public/' . $pdfPath)]);
        Mail::to('hr@example.com')->send(new FormSubmissionMail($pdfData, $emailAttachments));

        return redirect()->route('forms.show', ['formKey' => $formKey])
                        ->with('success', 'Form submitted successfully');
    }
}
