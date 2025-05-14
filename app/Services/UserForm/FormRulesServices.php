<?php

namespace App\Services\UserForm;

use App\Services\UserForm\FormConfigServices;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Service for handling form validation rules.
 */
class FormRulesServices
{
    protected FormConfigServices $formConfigServices;

    /**
     * Constructor to initialize FormConfigServices dependency.
     *
     * @param FormConfigServices $formConfigServices The form configuration service.
     */
    public function __construct(FormConfigServices $formConfigServices)
    {
        $this->formConfigServices = $formConfigServices;
    }

    /**
     * Generate default validation rules based on the field type.
     *
     * @param array $field The field configuration array.
     * @return array The validation rules for the field.
     */
    protected function defaultRules(array $field): array
    {
        $required = !empty($field["required"]);

        switch ($field["type"]) {
            case "radio":
                return [$required ? "required" : "nullable"];
            case "select":
                return [
                    $required ? "required" : "nullable",
                    Rule::in($field["options"] ?? []),
                ];
            case "email":
                return [$required ? "required" : "nullable", "email"];
            case "file":
                return $required
                    ? ["required", "file", 'mimes:jpg, jpeg, png, pdf, doc, docs, xls, xlsx', "max:5120"]
                    : ["nullable", "file", 'mimes:jpg, jpeg, png, pdf, doc, docs, xls, xlsx', "max:5120"];
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
                // Regular expression for validating phone numbers
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

    /**
     * Generate validation rules for a given form configuration.
     *
     * @param array $formData The submitted form data.
     * @param array $formConfig The form configuration array.
     * @return array The validation rules for the form fields.
     */
    public function validateRules(array $formData, array $formConfig): array
    {
        $rules = [];

        foreach ($formConfig as $name => $field) {
            // Use custom rules if provided; otherwise, generate default rules
            $fieldRules = !empty($field["rules"])
                ? $field["rules"]
                : $this->defaultRules($field);

            // Add conditional rules if applicable
            if (isset($field["conditional-rules"]["when"])) {
                $condition = $field["conditional-rules"]["when"];
                $dependentField = $condition["field"];
                $dependentValue = $condition["value"];

                // Retrieve the actual value of the dependent field
                $actualValue = isset($formData[$dependentField])
                    ? $formData[$dependentField]
                    : null;

                // Check if the condition is met for checkbox-group fields
                if (is_array($actualValue)) {
                    $conditionMet = in_array($dependentValue, $actualValue);
                } else {
                    $conditionMet = $actualValue === $dependentValue;
                }

                // Apply conditional rules if the condition is met
                if ($conditionMet) {
                    $fieldRules = array_merge($fieldRules, $condition["rules"]);
                }
            }

            // Remove duplicate rules
            $fieldRules = array_unique($fieldRules, SORT_REGULAR);

            // Assign rules to the field
            $rules[$name] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Validate form data against the generated rules.
     *
     * @param array $formData The submitted form data.
     * @param array $rules The validation rules.
     * @return array|\Illuminate\Contracts\Validation\Validator The validated data or validator instance if validation fails.
     */
    public function validator(array $formData, array $rules)
    {
        $validator = Validator::make($formData, $rules);

        // If validation fails, return the validator instance
        if ($validator->fails()) {
            return $validator;
        }

        $validatedData = $validator->validate();

        // Handle date range fields by merging them into a single field
        if (isset($validatedData['date-range-start']) && isset($validatedData['date-range-end'])) {
            $validatedData['date-range'] = 'From ' . $validatedData['date-range-start'] .  ' to ' . $validatedData['date-range-end'];
            unset($validatedData['date-range-start'], $validatedData['date-range-end']);
        }

        return $validatedData;
    }
}
