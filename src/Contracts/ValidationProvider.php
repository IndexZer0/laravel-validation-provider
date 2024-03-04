<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Contracts;

use Illuminate\Contracts\Validation\Validator;

interface ValidationProvider
{
    /*
     * --------------------------------
     * Core
     * --------------------------------
     */
    public function rules(): array;
    public function messages(): array;
    public function attributes(): array;

    /*
     * --------------------------------
     * Nesting Support
     * --------------------------------
     */
    public function dependentField(string $otherField): string;
    public function prependNestedKey(string $nestedKey): void;

    /*
     * --------------------------------
     * Convenience methods
     * --------------------------------
     */
    public function createValidator(array $data): Validator;
    public function validate(array $data): array;

    /*
     * --------------------------------
     * Fluent API
     * --------------------------------
     */
    public function nested(string $nestedKey): ValidationProvider;
    public function nestedArray(string $nestedKey): ValidationProvider;
    public function with(string|ValidationProvider $validationProvider): ValidationProvider;
}
