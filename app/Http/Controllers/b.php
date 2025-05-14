<?php

namespace App\Http\Controllers;

use App\Mail\UserForms\FormSubmissionMail;
use App\Services\UserForm\FormConfigServices;
use App\Services\UserForm\FormRulesServices;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Mail;
use Storage;
use Str;

class UserFormController extends Controller
{
    protected $formConfigServices;
    protected $formRulesService;

    public function __construct(FormConfigServices $formConfigServices, FormRulesServices $formRulesService)
    {
        $this->formConfigServices = $formConfigServices;
        $this->formRulesService = $formRulesService;
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
        $formConfig = $this->formConfigServices->getFormConfig($formKey);

        return view("forms.show", [
            "formKey" => $formKey,
            "formConfig" => $formConfig,
            "formComponents" => $this->formConfigServices->extractFieldsWithType($formConfig),
        ]);
    }

    public function index()
    {
        $forms = $this->formConfigServices->getAllForms();
        return view('forms.index', compact('forms'));
    }

    public function submit(Request $request, $formKey)
    {
        $formComponents = $this->formConfigServices->getFormConfig($formKey);
        $formConfig = $this->formConfigServices->extractFieldsWithType($formComponents);

        $formData = $request->all();
        $rules = $this->formRulesService->validateRules($formData, $formConfig);
        $validatedData = $this->formRulesService->validator($formData, $rules);
        if ($validatedData instanceof \Illuminate\Validation\Validator && $validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
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
        $formName = $formComponents['title'] ?? 'Default form name';
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
            'description' => $formComponents['description'] ?? '',
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
