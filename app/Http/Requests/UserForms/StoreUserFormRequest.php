<?php

namespace App\Http\Requests\UserForms;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $formKey = $this->input("formKey");
        $formConfig = config("userforms.{$formKey}.fields") ?? [];

        $rules = [
            "formKey" => ["required", "string"],
            "mailRecipients" => ["required", "email"],
            "ccRecipients" => ["nullable", "email"],
        ];

        foreach ($formConfig as $name => $field) {
            if (!isset($field["type"])) {
                continue;
            }

            $isRequired = !empty($field["required"]) ? "required" : "nullable";

            $typeRules = match ($field["type"]) {
                "checkbox" => ["nullable"],
                "checkbox-group" => ["array", "nullable"],
                "date" => ["date"],
                "email" => ["email"],
                "file" => ["mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,csv", "max:5120"],
                "number" => ["numeric"],
                "tel" => ['regex:/^\\+?\\d{1,4}?[-.\\s]?\\(?\\d{1,3}?\\)?[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,9}$/'],
                "text" => ["string", "max:255"],
                "textarea" => ["string"],
                "radio" => ["nullable"],
                "select" => ["nullable"],
                "url" => ["url", "active_url"],
                "notes" => ["string"],
                default => ["string"],
            };

            $customRules = $field["rules"] ?? [];

            $rules[$name] = array_merge([$isRequired], $typeRules, $customRules);
        }
        return $rules;
    }
}
