<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

class AggregateValidationProvider extends AbstractValidationProvider
{
    private readonly array $validationProviders;

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

    public function prependNestedKey(string $nestedKey, bool $increaseLevel): void
    {
        parent::prependNestedKey($nestedKey, $increaseLevel);
        foreach ($this->validationProviders as $validationProvider) {
            $validationProvider->prependNestedKey($nestedKey, true);
        }
    }
}
