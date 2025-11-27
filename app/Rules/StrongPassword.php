<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    private $minLength;
    private $requireUppercase;
    private $requireLowercase;
    private $requireNumbers;
    private $requireSpecial;

    /**
     * Create a new rule instance.
     *
     * @param  int     $minLength
     * @param  bool    $requireUppercase
     * @param  bool    $requireLowercase
     * @param  bool    $requireNumbers
     * @param  bool    $requireSpecial
     */
    public function __construct(
        int $minLength = 8,
        bool $requireUppercase = true,
        bool $requireLowercase = true,
        bool $requireNumbers = true,
        bool $requireSpecial = true
    ) {
        $this->minLength = $minLength;
        $this->requireUppercase = $requireUppercase;
        $this->requireLowercase = $requireLowercase;
        $this->requireNumbers = $requireNumbers;
        $this->requireSpecial = $requireSpecial;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check minimum length
        if (strlen($value) < $this->minLength) {
            $fail("The :attribute must be at least {$this->minLength} characters.");
            return;
        }

        // Check for uppercase character
        if ($this->requireUppercase && !preg_match('/[A-Z]/', $value)) {
            $fail("The :attribute must contain at least one uppercase letter.");
            return;
        }

        // Check for lowercase character
        if ($this->requireLowercase && !preg_match('/[a-z]/', $value)) {
            $fail("The :attribute must contain at least one lowercase letter.");
            return;
        }

        // Check for number
        if ($this->requireNumbers && !preg_match('/[0-9]/', $value)) {
            $fail("The :attribute must contain at least one number.");
            return;
        }

        // Check for special character
        if ($this->requireSpecial && !preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail("The :attribute must contain at least one special character.");
            return;
        }
    }
}
