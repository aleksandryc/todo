<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (!form) return;

    // Инициализация серверных ошибок
    document.querySelectorAll('.text-red-500').forEach(errorElement => {
        const field = errorElement.previousElementSibling;
        if (field && field.matches('input, textarea, select')) {
            validateField(field);
        }
    });

    // Обработчики событий
    const fields = form.querySelectorAll('input, textarea, select, [data-required]');
    fields.forEach(field => {
        const events = ['input', 'blur'];
        if (['radio', 'checkbox'].includes(field.type)) events.push('change');
        events.forEach(event => field.addEventListener(event, () => validateField(field)));
    });

    // Валидация при отправке
    form.addEventListener('submit', function(e) {
        let isValid = true;
        fields.forEach(field => {
            if (!validateField(field)) isValid = false;
        });
        if (!isValid) {
            e.preventDefault();
            form.querySelector('.invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Функции валидации
    function validateField(field) {
        let isValid = checkValidity(field);
        updateStyles(field, isValid);
        return isValid;
    }

    function checkValidity(field) {
        if (field.type === 'checkbox' && field.name.endsWith('[]')) {
            const checkboxes = document.querySelectorAll(`input[name="${field.name}"]`);
            return checkboxes[0].hasAttribute('data-required') 
                ? Array.from(checkboxes).some(cb => cb.checked)
                : true;
        } else if (field.type === 'radio') {
            const radios = document.querySelectorAll(`input[name="${field.name}"]`);
            return field.required ? Array.from(radios).some(r => r.checked) : true;
        }
        return field.checkValidity();
    }

    function updateStyles(field, isValid) {
        const container = field.closest('div');
        const errorMessage = field.parentNode.querySelector('.error-message');

        if (isValid) {
            field.classList.remove('invalid');
            container?.classList.remove('bg-[#fecaca]/25', 'border-[#f87171]');
            errorMessage?.remove();
        } else {
            field.classList.add('invalid');
            container?.classList.add('bg-[#fecaca]/25', 'border-[#f87171]');
            showErrorMessage(field, errorMessage);
        }
    }

    function showErrorMessage(field, existingError) {
        const message = getErrorMessage(field);
        if (!existingError) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        } else {
            existingError.textContent = message;
        }
    }

    function getErrorMessage(field) {
        if (field.validity.valueMissing) return 'This field is required.';
        if (field.validity.typeMismatch) {
            if (field.type === 'email') return 'Invalid email format.';
            if (field.type === 'url') return 'Invalid URL format.';
        }
        return 'Invalid input.';
    }
});
</script>
<?php

namespace App\Http\Controllers;

use App\Mail\UserForms\FormSubmissionMail;
use App\Services\UserForm\FormConfigServices;
use App\Services\UserForm\FormOutputServices;
use App\Services\UserForm\FormRulesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail as FacadesMail;

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
        return view('forms.index', compact('forms'));
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

        return view("forms.show", [
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
        $formData = $request->all();
        $formComponents = $this->formConfigServices->getFormConfig($formKey);
        $formConfig = $this->formConfigServices->extractFieldsWithType($formComponents);

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
        $pdfForEmail = $this->formOutputServices->generatePdf($formName, $validatedData, $embeddedImages);

        // Send confirmation email with attachments
        FacadesMail::to("admin@example.com")->send(new FormSubmissionMail($pdfForEmail[0], $pdfForEmail[1], $attachments));

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
