<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class GuestOrCustomMemberRole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== "guest") {
            if (!\Ramsey\Uuid\Uuid::isValid($value)) {
                $fail("The $attribute must be a valid UUID or 'guest'.");
            } elseif (!Validator::make(['uuid' => $value], ['uuid' => new BinaryUuidExists('member_roles')])->passes()) {
                $fail("The selected $attribute is invalid.");
            }
        }
    }
}
