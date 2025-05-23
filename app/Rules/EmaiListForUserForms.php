<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmaiListForUserForms implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if (empty($value)) {
            return;
        }

        $emails = array_map('trim', explode(',', $value));

        $uniqueEmails = array_unique($emails);
        if (count($emails) !== count($uniqueEmails)) {
            $fail('Field :attribute contains duplicated email.');
            return;
        }

        $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        foreach ($emails as $email) {
            if (empty($email) || !preg_match($emailRegex, $email)) {
                $fail('Field :attribute contains invalid email.');
                return;
            }
        }
    }

}
