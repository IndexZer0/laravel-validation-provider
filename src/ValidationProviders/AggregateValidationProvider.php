<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Traits\HasValidationProviderChildren;
use IndexZer0\LaravelValidationProvider\ValidationProviderFactory;

class AggregateValidationProvider extends AbstractValidationProvider
{
    use HasValidationProviderChildren;

    public function __construct(ValidationProvider ...$validationProviders)
    {
        $this->validationProviders = $validationProviders;
    }

    /*
     * --------------------------------
     * Core
     * --------------------------------
     */

    public function rules(): array
    {
        $rules = [];

        foreach ($this->validationProviders as $validationProvider) {
            $rules = array_merge_recursive($rules, $validationProvider->rules());
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];

        foreach ($this->validationProviders as $validationProvider) {
            $messages = array_merge($messages, $validationProvider->messages());
        }

        return $messages;
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach ($this->validationProviders as $validationProvider) {
            $attributes = array_merge($attributes, $validationProvider->attributes());
        }

        return $attributes;
    }

    /*
     * --------------------------------
     * Fluent API
     * --------------------------------
     */

    public function with(string|ValidationProvider $validationProvider): ValidationProvider
    {
        if (is_string($validationProvider)) {
            $validationProvider = ValidationProviderFactory::instantiateValidationProvider($validationProvider);
        }

        return new self($validationProvider, ...$this->validationProviders);
    }
}
