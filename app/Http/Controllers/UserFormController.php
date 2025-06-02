<?php

namespace App\Http\Controllers;

use App\Mail\UserForms\FormSubmissionMail;
use App\Services\UserForm\FormConfigServices;
use App\Services\UserForm\FormOutputServices;
use App\Services\UserForm\FormRulesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Inertia\Inertia;

/**
 * Controller for managing user form submissions.
 */
class UserFormController extends Controller
{
    protected $formConfigServices;
    protected $formRulesService;
    protected $formOutputServices;

    /**
     * Constructor to initialize services.
     *
     * @param FormConfigServices $formConfigServices Handles form configurations.
     * @param FormRulesServices $formRulesService Handles validation rules.
     * @param FormOutputServices $formOutputServices Handles form output processing.
     */
    public function __construct(FormConfigServices $formConfigServices, FormRulesServices $formRulesService, FormOutputServices $formOutputServices)
    {
        $this->formConfigServices = $formConfigServices;
        $this->formRulesService = $formRulesService;
        $this->formOutputServices = $formOutputServices;
    }

    /**
     * Display a list of available forms.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $forms = $this->formConfigServices->getAllForms();
        return Inertia::render('Forms/Index', ['forms' => $forms]);
    }

    /**
     * Show the form submission page.
     *
     * @param string $formKey The form identifier.
     * @return \Illuminate\View\View
     */
    public function show($formKey)
    {
        // Retrieve form configuration
        $formConfig = $this->formConfigServices->getFormConfig($formKey);

        return Inertia::render("Forms/Show", [
            "formKey" => $formKey,
            "formConfig" => $formConfig,
            "formComponents" => $this->formConfigServices->extractFieldsWithType($formConfig),
        ]);
    }

    /**
     * Handle form submission and processing.
     *
     * @param Request $request The request object containing user inputs.
     * @param string $formKey The form identifier.
     * @return \Illuminate\Http\RedirectResponse Redirects to the main page after submission.
     */
    public function submit(Request $request, $formKey)
    {
        $mailRecipients = [$request->mailRecipient];
        $ccRecipients = $request->ccRecipient ? array_map('trim', explode(',', $request->ccRecipient)) : [];

        dd($formData = $request->all());
        $formComponents = $this->formConfigServices->getFormConfig($formKey);
        $formConfig = $this->formConfigServices->extractFieldsWithType($formComponents);
        $hiddenFields = $formComponents['_hidden_fields'] ?? [];

        // Validate form input
        $rules = $this->formRulesService->validateRules($formData, $formConfig);
        $validatedData = $this->formRulesService->validator($formData, $rules);

        // Redirect if validation fails
        if ($validatedData instanceof \Illuminate\Validation\Validator && $validatedData->fails()) {
            return redirect()->back()->withErrors($validatedData)->withInput();
        }

        [$embeddedImages, $attachments] = [[], []];

        // Handle file uploads
        foreach ($request->allFiles() as $fieldName => $file) {
            [$filePathStr, $embed] = $this->formOutputServices->handleUploadFile($file);
            $formData[$fieldName] = $filePathStr;

            if ($embed !== null) {
                $embeddedImages[$fieldName] = $embed;
            }
            $attachments[] = $filePathStr;
        }

        // Store form data as JSON
        $formName = $formComponents['title'] ?? 'Default form name';
        $this->formOutputServices->storeFormDataAsJson($formName, $validatedData);

        // Generate a PDF for submission
        $pdfForEmail = $this->formOutputServices->generatePdf($formName, $validatedData, $embeddedImages, $hiddenFields);

        // Send confirmation email with attachments
        FacadesMail::send(new FormSubmissionMail($pdfForEmail[0], $pdfForEmail[1], $attachments, $mailRecipients, $ccRecipients));

        return redirect('/')->with('message', 'Form successfully submitted!');

        // Store in database (Needs table and model setup)
        /*
         This code will save the form submission as a JSON string in the database:
         SubmittedForm::create([
            'form_name' => $formName ?? 'Untitled Form',
            'form_json' => json_encode($validatedData, JSON_PRETTY_PRINT),
        ]);
        */
    }
}
