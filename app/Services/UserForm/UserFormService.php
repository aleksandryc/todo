<?php

namespace App\Services\UserForm;

use App\Mail\UserForms\UserFormSubmission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Arr;

class UserFormService
{
    public function listAllForms(): array
    {
        return collect(config("userforms", []))->map(function ($form, $key) {
            return [
                "formKey" => $key,
                "title" => $form["title"] ?? "No title",
                "description" => $form["description"] ?? "",
            ];
        })->toArray();
    }

    public function userFormConfig($formKey): array
    {
        $formComponents = $this->extractFieldsWithType(config("userforms.$formKey"));
        $formComponents["formKey"] = $formKey;
        return $formComponents;
    }

    public function extractFieldsWithType($formConfig)
    {
        $result = [
            "title" => Arr::get($formConfig, "title", ""),
            "description" => Arr::get($formConfig, "description", ""),
            "fields" => [],
        ];

        if (isset($formConfig["fields"]) && is_array($formConfig["fields"])) {
            $result["fields"] = $this->extractFieldsRecursively($formConfig["fields"]);
        }

        foreach ($formConfig as $key => $value) {
            if (is_array($value) && $key !== "fields") {
                $nested = $this->extractFieldsWithType($value);
                unset($nested["title"], $nested["description"]);
                $result = array_merge($result, $nested);
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
                if (isset($value["type"])) {
                    $result[$key] = $value;
                } else {
                    $result = array_merge(
                        $result,
                        $this->extractFieldsRecursively($value)
                    );
                }
            }
        }

        return $result;
    }

    public function formSubmission($validatedData)
    {
        $formKey = $validatedData["formKey"];
        $title = config("userforms.$formKey.title", []);
        $hiddenFields = collect(config("userforms.$formKey.fields._hidden_fields", []));
        if (!empty($hiddenFields)) {
            $hiddenFields = $hiddenFields->toArray();
        }

        $dataForSubmission = [
            "title" => $title,
            "fields" => collect(Arr::except($validatedData, ["formKey", "mailRecipients", "ccRecipients"]))->filter(function ($value) {
                return !is_null($value) && $value !== '' && !(is_array($value) && empty($value));
            }),
            "hiddenFields" => $hiddenFields,
        ];

        $primaryRecipients = array_filter(Arr::wrap(Arr::get($validatedData, "mailRecipients")));
        $ccRecipients = array_filter(Arr::wrap(Arr::get($validatedData, "ccRecipients", [])));
        $pdf = PDF::loadView("userforms.pdf", $dataForSubmission)
            ->setPaper("a4")
            ->setWarnings(false)
            ->output();


        if (empty($primaryRecipients)) {
            throw new \RuntimeException("Primary recipient email is required");
        }

        foreach ($primaryRecipients as $recipient) {
            if (!empty($recipient)) {
                Mail::to($recipient)->send(new UserFormSubmission($dataForSubmission, $pdf));
            }
        }

        foreach ($ccRecipients as $ccRecipient) {
            if (!empty($ccRecipient)) {
                Mail::to($ccRecipient)->send(new UserFormSubmission($dataForSubmission, $pdf));
            }
        }

        return json_encode([
            "form_name" => $dataForSubmission["title"],
            "fields" => $dataForSubmission["fields"],
            "submitted_at" => now()->toDayDateTimeString(),
        ]);
    }
}
