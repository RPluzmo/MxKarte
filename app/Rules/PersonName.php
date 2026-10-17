<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PersonName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)
            || ! preg_match("/^[\\p{L}\\p{M}]+(?:[ '\\x{2019}-][\\p{L}\\p{M}]+)*$/u", $value)) {
            $fail(__('validation.person_name'));
        }
    }

    public static function normalize(mixed $value): mixed
    {
        if (! is_string($value)) {
            return null;
        }

        $value = preg_replace('/\\s+/u', ' ', trim($value));

        return $value === '' ? '' : mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
}
