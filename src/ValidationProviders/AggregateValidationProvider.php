<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

class AggregateValidationProvider extends AbstractValidationProvider
{
    private array $validationProviders;

    public function __construct(ValidationProvider ...$validationProviders)
    {
        $this->validationProviders = $validationProviders;
    }

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

    public function prependNestedKey(string $nestedKey): void
    {
        parent::prependNestedKey($nestedKey);
        foreach ($this->validationProviders as $validationProvider) {
            $validationProvider->prependNestedKey($nestedKey);
        }
    }

    public function with(string|ValidationProvider $validationProvider): ValidationProvider
    {
        if (is_string($validationProvider)) {
            if (!class_exists($validationProvider) || !is_a($validationProvider, ValidationProvider::class, true)) {
                throw new \Exception('Class must be a ValidationProvider');
            }
            $validationProvider = new $validationProvider;
        }

        $this->addValidationProvider($validationProvider);
        return $this;
    }

    public function addValidationProvider(ValidationProvider $validationProvider)
    {
        array_unshift($this->validationProviders, $validationProvider);
    }
}
