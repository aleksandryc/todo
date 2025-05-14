<?php

namespace App\Services\UserForm;

/**
 * Service for handling form configuration.
 */
class FormConfigServices
{
    /**
     * Get form configuration by key.
     *
     * @param string $formKey The key of the form configuration.
     * @return array The form configuration array.
     */
    public function getFormConfig(string $formKey): array
    {
        // Retrieve the form configuration from config/forms.php by key
        // If the form is not found, return a 404 error response
        return config("forms.$formKey") ?? abort(404, 'Form Not Found');
    }

    /**
     * Extract all fields with a 'type' key from the form configuration.
     *
     * @param array $formConfig The form configuration array.
     * @return array Array of fields that have a 'type' key.
     */
    public function extractFieldsWithType($formConfig)
    {
        $result = [];

        // Check if 'fields' key exists and is an array
        if (isset($formConfig["fields"]) && is_array($formConfig["fields"])) {
            // Extract fields recursively from the 'fields' array
            $result = $this->extractFieldsRecursively($formConfig["fields"]);
        }

        // Recursively search for fields with 'type' in other nested arrays except 'fields'
        foreach ($formConfig as $key => $value) {
            if (is_array($value) && $key !== "fields") {
                // Merge results from nested arrays
                $result = array_merge(
                    $result,
                    $this->extractFieldsWithType($value),
                );
            }
        }

        return $result;
    }

    /**
     * Recursively extract fields that contain a 'type' key.
     *
     * @param array $fields The fields array.
     * @return array Array of fields with a 'type' key.
     */
    protected function extractFieldsRecursively($fields)
    {
        $result = [];

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                // If the field has a 'type' key, add it to the result
                if (isset($value["type"])) {
                    $result[$key] = $value;
                } else {
                    // Continue searching recursively in nested arrays
                    $result = array_merge(
                        $result,
                        $this->extractFieldsRecursively($value),
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve all available forms with their keys, titles, and descriptions.
     *
     * @return array List of forms with key, title, and description.
     */
    public function getAllForms(): array
    {
        // Retrieve all forms from the configuration file
        $allForms = config('forms', []);

        // Map each form to extract its key, title, and description
        $forms = collect($allForms)->map(function ($form, $key) {
            return [
                'key' => $key,
                'title' => $form['title'] ?? "Untitled Form",
                'description' => $form['description'] ?? '',
            ];
        })->values()->all();

        return $forms;
    }
}
