<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use Illuminate\Contracts\Validation\Validator;
use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviderFactory;

abstract class AbstractValidationProvider implements ValidationProvider
{
    protected array $nestedKey = [];

    /*
     * --------------------------------
     * Core
     * --------------------------------
     */

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }

    /*
     * --------------------------------
     * Nesting Support
     * --------------------------------
     */

    public function dependentField(string $otherField): string
    {
        $nestedKey = $this->getNestedKeyDotNotation();

        if ($nestedKey === '') {
            return $otherField;
        }

        return join('.', [$nestedKey, $otherField]);
    }

    public function prependNestedKey(string $nestedKey): void
    {
        array_unshift($this->nestedKey, $nestedKey);
    }

    /*
     * --------------------------------
     * Convenience methods
     * --------------------------------
     */

    public function createValidator($data): Validator
    {
        return \Illuminate\Support\Facades\Validator::make(
            $data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }

    public function validate($data): array
    {
        return ($this->createValidator($data))->validate();
    }

    /*
     * --------------------------------
     * Fluent API
     * --------------------------------
     */

    public function nested(string $nestedKey): ValidationProvider
    {
        return new NestedValidationProvider($nestedKey, $this);
    }

    public function nestedArray(string $nestedKey): ValidationProvider
    {
        return new ArrayValidationProvider($nestedKey, $this);
    }

    public function with(string|ValidationProvider $validationProvider): ValidationProvider
    {
        if (is_string($validationProvider)) {
            $validationProvider = ValidationProviderFactory::instantiateValidationProvider($validationProvider);
        }

        return new AggregateValidationProvider($validationProvider, $this);
    }

    public function exclude(array $attributes): ValidationProvider
    {
        return new ExcludeAttributesValidationProvider($attributes, $this);
    }

    /*
     * --------------------------------
     * Helpers
     * --------------------------------
     */

    protected function getNestedKeyDotNotation(): string
    {
        return join('.', $this->nestedKey);
    }
}
