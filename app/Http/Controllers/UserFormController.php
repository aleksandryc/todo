<?php

namespace App\Http\Controllers;

use App\Mail\UserForms\FormSubmissionMail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
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
            "external-access-request" => [
                "title" => "External Access Request",
                "description" =>
                "To request an employee be granted external access to company information when outside of Elias Woodwork’s facilities please fill in the following and submit to HR for approval. If a mobile phone or laptop is required, please note that in the devices section. ",
                "fields" => [
                    "names-group" => [
                        "supervisor" => [
                            "name" => "supervisor",
                            "label" => "Supervisor",
                            "type" => "text",
                            "rules" => ["required", "max:255"],
                            "placeholder" => "Enter Full name",
                            "required" => true,
                        ],
                        "employee" => [
                            "name" => "employee",
                            "label" => "Employee",
                            "type" => "text",
                            "rules" => ["required", "max:255"],
                            "placeholder" => "Enter Full name",
                            "required" => true,
                        ],
                    ],
                    "access-type" => [
                        "name" => "access-type",
                        "label" => "Type of Access (Check all that apply) ",
                        "type" => "checkbox-group",
                        "options" => [
                            "External Email Access",
                            "External VPN Access",
                        ],
                        "rules" => [
                            "required",
                            "array",
                            Rule::in([
                                "External Email Access",
                                "External VPN Access",
                            ]),
                        ],
                        "required" => true,
                    ],
                    [
                        "Device-used-email" => [
                            "label" =>
                            "Devices being used (MFA requires mobile phone): ",
                            "type" => "text",

                            "placeholder" => "MFA requires mobile phone",
                            "conditional-rules" => [
                                "when" => [
                                    "field" => "access-type",
                                    "value" => "External Email Access",
                                    "rules" => ["required", "max:255"],
                                ],
                            ],
                        ],
                        "device-used-vpn" => [
                            "label" => "Devices being used: ",
                            "type" => "text",

                            "placeholder" => "MFA requires mobile phone",
                            "conditional-rules" => [
                                "when" => [
                                    "field" => "access-type",
                                    "value" => "External VPN Access",
                                    "rules" => ["required", "max:255"],
                                ],
                            ],
                        ],
                    ],
                    "date-range-start" => [
                        "label" =>
                        "Timeframe of approval (Provide start) ",
                        "type" => "date",
                        "rules" => ["nullable", "date"],
                        "conditional-rules" => [
                            "when" => [
                                "field" => "date-range-perm",
                                "value" => false,
                                "rules" => ["required", "date"],
                            ],
                        ],
                    ],
                    "date-range-end" => [
                        "label" =>
                        "Timeframe of approval (Provide end dates) ",
                        "type" => "date",
                        "rules" => [
                            "nullable",
                            "date",
                            "after_or_equal:date-range-start",
                        ],
                        "required" => false,
                        "conditional-rules" => [
                            "when" => [
                                "field" => "date-range-perm",
                                "value" => false,
                                "rules" => ["required", "date"],
                            ],
                        ],
                    ],
                    "date-range-perm" => [
                        "label" =>
                        "Timeframe of approval (or “Permanent”) ",
                        "type" => "checkbox",
                        "options" => "“Permanent”",
                        "rules" => ["boolean"],
                        "required" => false,
                    ],
                    "reason" => [
                        "label" =>
                        "Reason (Provide a brief description of why this is needed)",
                        "type" => "textarea",
                        "required" => true,
                    ],
                    "logo" => [
                        "label" => "Attach a file",
                        "type" => "file",
                        "rules" => ["nullable", "file", "max:2048"],
                        "required" => false,
                    ],
                ],
            ],
            "new-employee" => [
                "title" => "Add New Employee Form",
                "fields" => [
                    "name" => [
                        "label" => "Full name",
                        "type" => "text",
                        "rules" => ["required", "max:255"],
                        "placeholder" => "Enter Full name",
                    ],
                    "shift" => [
                        "label" => "Shift",
                        "type" => "select",
                        "options" => [
                            "Morden Day",
                            "Morden Evening",
                            "Office",
                            "Winkler Day",
                            "Winkler Evening",
                            "Driver",
                            "Cleaning",
                        ],
                        "rules" => ["required"],
                        "required" => true,
                    ],
                    "phone" => [
                        "label" => "Phone",
                        "type" => "tel",
                        "rules" => [
                            "required",
                            'regex:/^\\+?\\d{1,4}?[-.\\s]?\\(?\\d{1,3}?\\)?[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,9}$/',
                        ],
                        "placeholder" => "Enter phone number",
                    ],
                    "status" => [
                        "label" => "Active",
                        "type" => "radio",
                        "options" => ["Yes", "No"],
                        "rules" => ["required"],
                    ],
                    "supervisor" => [
                        "label" => "Supervisor",
                        "type" => "text",
                        "rules" => ["required", "max:255"],
                        "placeholder" => "Enter Full name of supervisor",
                    ],
                    "file" => [
                        "label" => "Photo",
                        "type" => "file",
                    ],
                ],
            ],
            "new-course" => [
                "title" => "Add New Course Form",
                "fields" => [
                    "email" => [
                        "label" => "Email",
                        "type" => "email",
                        "rules" => [
                            "required",
                            "email",
                            'regex: /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                        ],
                        "placeholder" => "Enter email",
                        "required" => true,
                    ],
                    "name" => [
                        "label" => "Course name",
                        "type" => "textarea",
                        "rules" => ["required", "max:255"],
                        "required" => true,
                    ],

                    "date" => [
                        "label" => "Hard Expiry Date",
                        "type" => "date",
                        "rules" => ["required"],
                        "required" => true,
                    ],

                    "valid" => [
                        "label" => "Default Valid Period",
                        "type" => "date", //TODO Four fields need to be added for chosing period (Years, Month, Weeks, Days)
                        "rules" => ["nullable"],
                    ],
                    "logo" => [
                        "label" => "Attach a file",
                        "type" => "file",
                        "rules" => ["nullable", "file", "max:2048"],
                        "required" => false,
                    ],
                    "type" => [
                        "label" => "Type of course",
                        "type" => "select",
                        "options" => [
                            "Policies",
                            "Toolbox Talks",
                            "Manitoba SAFE Work Courses",
                        ],
                        "rules" => ["required"],
                        "required" => true,
                    ],
                    "website" => [
                        "label" => "Link to website",
                        "type" => "url",
                        "rules" => ["nullable", "url"],
                        "required" => false,
                        "placeholder" => "Example: https://example.com",
                    ],
                    "attachment" => [
                        "label" => "Attach a file",
                        "type" => "file",
                        "rules" => ["nullable", "file", "max:2048"],
                        "required" => false,
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
        if (isset($formConfig["fields"]) && is_array($formConfig["fields"])) {
            $result = $this->extractFieldsRecursively($formConfig["fields"]);
        }

        foreach ($formConfig as $key => $value) {
            if (is_array($value) && $key !== "fields") {
                $result = array_merge(
                    $result,
                    $this->extractFieldsWithType($value),
                );
            }
        }
        return $result;
    }

    protected function extractFieldsRecursively($fields)
    {
        $result = [];

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                if (isset($value["type"])) {
                    $result[$key] = $value;
                }
                $result = array_merge(
                    $result,
                    $this->extractFieldsRecursively($value),
                );
            }
        }

        return $result;
    }
    public function show($formKey)
    {
        // Get form name from url
        $formConfig = $this->getFormConfig($formKey);

        if (!$formConfig) {
            abort(404, "Form not found");
        }
        return view("forms.show", [
            "formKey" => $formKey,
            "formConfig" => $formConfig,
            "formComponents" => $this->extractFieldsWithType($formConfig),
        ]);
    }

    protected function validateRules($formData, $formConfig)
    {
        $rules = [];
        foreach ($formConfig as $name => $field) {
            $fieldRules = [];
            //Use existing rules
            if (!empty($field["rules"])) {
                $fieldRules = $field["rules"];
            } else {
                //Automated generated rules, if 'rules' dose not exist
                switch ($field["type"]) {
                    case "radio":
                        $fieldRules = ["nullable", "boolean"];
                        break;
                    case "select":
                        $fieldRules = [
                            $field["required"] ? "required" : "nullable",
                            Rule::in($field["options"]),
                        ];
                        break;
                    case "email":
                        $fieldRules = [
                            $field["required"] ? "required" : "nullable",
                            "email",
                        ];
                        break;
                    case "file":
                        $fieldRules =
                            isset($field["required"]) && $field["required"]
                            ? ["required", "file", "max:5120"]
                            : ["nullable", "file", "max:5120"];
                        break;
                    case "checkbox":
                        $fieldRules = ["nullable", "boolean"];
                        break;
                    case "checkbox-group":
                        $fieldRules = [
                            "nullable",
                            "array",
                            Rule::in($field["options"]),
                        ];
                        break;
                    case "date":
                        $fieldRules =
                            isset($field["required"]) && $field["required"]
                            ? ["required", "date"]
                            : ["nullable", "date"];
                        break;
                    case "textarea":
                        $fieldRules =
                            isset($field["required"]) && $field["required"]
                            ? ["required", "string", "max:1000"]
                            : ["nullable", "string", "max:1000"];
                        break;
                    case "tel":
                        $fieldRules =
                            isset($field["required"]) && $field["required"]
                            ? [
                                "required",
                                'regex:/^\\+?\\d{1,4}?[-.\\s]?\\(?\\d{1,3}?\\)?[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,9}$/',
                            ]
                            : [
                                "nullable",
                                'regex:/^\\+?\\d{1,4}?[-.\\s]?\\(?\\d{1,3}?\\)?[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,9}$/',
                            ];
                        break;
                    case "url":
                        $fieldRules =
                            isset($field["required"]) && $field["required"]
                            ? ["required", "url"]
                            : ["nullable", "url"];
                        break;
                    default:
                        $fieldRules =
                            isset($field["required"]) && $field["required"]
                            ? ["required", "string", "max:255"]
                            : ["nullable", "string", "max:255"];
                        break;
                }
            }
            //Add conditional rules if applicable
            if (isset($field["conditional-rules"]["when"])) {
                $condition = $field["conditional-rules"]["when"];
                $dependentField = $condition["field"];
                $dependentValue = $condition["value"];

                //For chexbox-group
                $actualValue = isset($formData[$dependentField])
                    ? $formData[$dependentField]
                    : null;
                if (is_array($actualValue)) {
                    $conditionMet = in_array($dependentValue, $actualValue);
                } else {
                    $conditionMet = $actualValue === $dependentValue;
                }
                if ($conditionMet) {
                    $fieldRules = array_merge($fieldRules, $condition["rules"]);
                }
                //Apply conditional rules
                if ($actualValue === $dependentValue) {
                    $fieldRules = array_merge($fieldRules, $condition["rules"]);
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
        $formConfig = $this->extractFieldsWithType(
            $this->getFormConfig($formKey),
        );
        if (!$formConfig) {
            abort(404, "Form Not Found");
        }
        $formData = $request->all();
        $rules = $this->validateRules($formData, $formConfig);

        $validator = Validator::make($formData, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validate();

        // Date interval in one field
        if (isset($validatedData['date-range-start']) && isset($validatedData['date-range-end'])) {
            $validatedData['date-range'] = 'From ' . $validatedData['date-range-start'] .  ' to ' . $validatedData['date-range-end'];
            unset($validatedData['date-range-start'], $validatedData['date-range-end']);
        }

        $embeddedImages = [];
        $attachments = [];
        $filePath = [];

        // Save upload file
        foreach ($formConfig as $name => $field) {
            if ($field['type'] === 'checkbox' || $field['type'] === 'checkbox-group' && empty($validatedData[$name])) {
                unset($validatedData[$name]);
            }
            if ($field['type'] === 'file' && $request->hasFile($name)) {
                $uploadedfile = $request->file($name);
                $fileName = Str::random(16) . '.' . $uploadedfile->getClientOriginalExtension();
                $filePathStr = $uploadedfile->storeAs('attachments', $fileName, 'public');
                $filePath[$name] = $filePathStr; //Adding file path to form

                $fullPath = storage_path('app/public/' . $filePathStr);
                $attachments[$name] = 'app/public/attachments/' . $fileName;

                //Prepare embedded image
                if (Str::startsWith(File::mimeType($fullPath), 'image/')) {
                    $mimeType = File::mimeType($fullPath);
                    $base64 = 'data:' . $mimeType . ';base64,' . base64_encode(File::get($fullPath));
                    $embeddedImages[$name] = $base64;
                }
            }
        }
        $validatedData['files'] = $filePath;
        $validatedData['embedded-images'] = $embeddedImages;

        //Stoere in JSON
        $formName = $this->getFormConfig($formKey)['title'];
        $jsonPath = storage_path("app/public/forms/");
        $formDataWithName = [
            "form_name" => $formName ?? "untitled",
            "submitted_at" => now()->toDayDateTimeString(),
            "fields" => $validatedData,
        ];
        if (!File::exists($jsonPath)) {
            File::makeDirectory($jsonPath, 0755, true);
        }
        $fileName = "form_" . now()->format("Ymd_His") . ".json";
        File::put(
            $jsonPath . $fileName,
            json_encode($formDataWithName, JSON_PRETTY_PRINT),
        );

        //store in db
        /* Need to create table and model,
         This code saved json string in db
         SubmittedForm::create([
            'form_name' => $formName ?? 'Untitled Form',
            'form_json' => $formDataWithName,
        ]);
        */

        // Preparation data for PDF
        $logo = 'data:' . File::mimeType(storage_path('app/public/logo-96x96.png')) . ';base64,' . base64_encode(File::get(storage_path('app/public/logo-96x96.png')));
        $cleanPDFData = [];
        foreach ($validatedData as $key => $value) {
            if ($value === [] || $value === '' || $value === null) {
                continue;
            } elseif ($key === 'embedded-images') {
                unset($cleanPDFData[$key]);
            } elseif ($key === 'files') {
                $list = [];
                foreach ($value as $k => $item){
                    $list[$k] = File::basename($item);
                }
                $cleanPDFData[$key] = $list;
            } else {
                $cleanPDFData[$key] = $value;
            }
        };
        $pdfData = [
            "title" => $formName,
            'logo' => $logo,
            'description' => $this->getFormConfig($formKey)['description'] ?? '',
            "fields" => $cleanPDFData,
            "embeddedImages" => $embeddedImages,
        ];

        // Generate PDF
        $pdf = Pdf::setPaper('a4')->loadView("forms.pdf", $pdfData);
        $pdf->setOptions(["isRemoteEnabled" => true]);

        // Save PDF in file
        $pdfContent = $pdf->output();
        $relativePath = "pdf/form_" . now()->format("Ymd_His") . ".pdf";
        Storage::disk("public")->put($relativePath, $pdfContent);
        $pdfAttachmentPath = "public/" . $relativePath;
        //dd($cleanPDFData);
        /* Mail::to("admin@example.com")->send(
            new FormSubmissionMail($pdfData, $pdfAttachmentPath, $attachments),
        ); */

        return $pdf->stream("form_submission.pdf");
        /* return redirect()->back()->with('message', 'Form successfully submitted!'); */
    }
}
