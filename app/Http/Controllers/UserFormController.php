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
        return config("forms.$formKey") ?? abort(404, 'Form Not Found');
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

    protected function handleUploadFile($file, $name)
    {
        $fileName = Str::random(16) . '.' . $file->getClientOriginalExtension();
        $filePathStr = $file->storeAs('/attachments', $fileName, 'public');

        $fullPath = storage_path('app/public/' . $filePathStr);
        $embed = null;

        if (Str::startsWith(File::mimeType($fullPath), 'image/')) {
            $mimeType = File::mimeType($fullPath);
            $embed = 'data:' . $mimeType . ';base64,' . base64_encode(File::get($fullPath));
        }

        return [$filePathStr, $embed];
    }

    public function show($formKey)
    {
        // Get form name from url
        $formConfig = $this->getFormConfig($formKey);

        return view("forms.show", [
            "formKey" => $formKey,
            "formConfig" => $formConfig,
            "formComponents" => $this->extractFieldsWithType($formConfig),
        ]);
    }

    public function index()
    {
        // Retrieve all forms key and other information
        $allForms = config('forms', []);

        // Get forms config key and name with description
        $forms = collect($allForms)->map(function ($form, $key) {
            return [
                'key' => $key,
                'title' => $form['title'] ?? "Untitled Form",
                'description' => $form['description'] ?? '',
            ];
        })->values()->all();

        return view('forms.index', compact('forms'));
    }
    protected function defaultRules(array $field)
    {
        $required = !empty($field["required"]);
        switch ($field["type"]) {
            case "radio":
                return [$required ? "required" : "nullable", "boolean"];
            case "select":
                return [
                    $required ? "required" : "nullable",
                    Rule::in($field["options"] ?? []),
                ];
            case "email":
                return [$required ? "required" : "nullable", "email"];
            case "file":
                return $required
                    ? ["required", "file", "max:5120"]
                    : ["nullable", "file", "max:5120"];
            case "checkbox":
                return [$required ? "required" : "nullable", "accepted", "boolean"];
            case "checkbox-group":
                return [
                    $required ? "required" : "nullable",
                    "array",
                    Rule::in($field["options"] ?? []),
                ];
            case "date":
                return $required
                    ? ["required", "date"]
                    : ["nullable", "date"];
            case "textarea":
                return $required
                    ? ["required", "string", "max:1000"]
                    : ["nullable", "string", "max:1000"];
            case "tel":
                $regex = 'regex:/^\\+?\\d{1,4}?[-.\\s]?\\(?\\d{1,3}?\\)?[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,9}$/';
                return $required
                    ? ["required", $regex]
                    : ["nullable", $regex];
            case "url":
                return $required
                    ? ["required", "url"]
                    : ["nullable", "url"];
            default:
                return $required
                    ? ["required", "string", "max:255"]
                    : ["nullable", "string", "max:255"];
        }
    }

    protected function validateRules(array $formData, array $formConfig)
    {
        $rules = [];

        foreach ($formConfig as $name => $field) {
            // Use custom rules if provided; otherwise generate default rules
            $fieldRules = !empty($field["rules"])
                ? $field["rules"]
                : $this->defaultRules($field);

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

            //Delete duplicates
            $fieldRules = array_unique($fieldRules, SORT_REGULAR);

            //Add rule for field
            $rules[$name] = $fieldRules;
        }
        return $rules;
    }
    public function submit(Request $request, $formKey)
    {
        $formComponents = $this->getFormConfig($formKey);
        $formConfig = $this->extractFieldsWithType($formComponents);

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

        [$embeddedImages, $attachments, $filePath] = [[], [], []];

        // Save upload file
        foreach ($formConfig as $name => $field) {
            if ($field['type'] === 'checkbox-group' && !isset($validatedData[$name])) {
                unset($validatedData[$name]);
            }
        }
        foreach ($request->allFiles() as $fieldName => $file) {
            [$filePathStr, $embed] = $this->handleUploadFile($file, $fieldName);
            $formData[$fieldName] = $filePathStr;

            if ($embed !== null) {
                $embeddedImages[$fieldName] = $embed;
            }
            $attachments[] = $filePathStr;
        }
        $validatedData['files'] = $filePath;
        $validatedData['embedded-images'] = $embeddedImages;

        //Stoere in JSON
        $formName = $this->getFormConfig($formKey)['title'] ?? 'Default form name';
        $jsonPath = storage_path("app/public/forms/");
        $formDataWithName = [
            "form_name" => $formName,
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
        // Preparation data for PDF
        $logo = 'data:' . File::mimeType(storage_path('app/public/logo-96x96.png')) . ';base64,' . base64_encode(File::get(storage_path('app/public/logo-96x96.png')));
        // Initialize an array to store cleaned and formatted data for generating the PDF
        $cleanPDFData = [];
        foreach ($validatedData as $key => $value) {
            if ($value === [] || $value === '' || $value === null) {
                continue;
            } elseif ($key === 'embedded-images') {
                // Skip processing for 'embedded-images' key
                unset($cleanPDFData[$key]);
            } elseif ($key === 'files') {
                // Transform file paths into their base names for better readability in the PDF
                $list = [];
                foreach ($value as $k => $item) {
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
        $pdf = Pdf::setPaper('a4')->loadView("forms.pdf", $pdfData)->setOptions(["isRemoteEnabled" => true]);

        // Save PDF in file
        $pdfContent = $pdf->output();
        $relativePath = "pdf/form_" . now()->format("Ymd_His") . ".pdf";
        Storage::disk("public")->put($relativePath, $pdfContent);
        $pdfAttachmentPath = "public/" . $relativePath;
        Mail::to("admin@example.com")->send(
            new FormSubmissionMail($pdfData, $pdfAttachmentPath, $attachments),
        );

        //return $pdf->stream("form_submission.pdf");
        return redirect('/')->with('message', 'Form successfully submitted!');
    }
}
