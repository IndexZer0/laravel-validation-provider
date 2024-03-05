<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

class CustomValidationProvider extends AbstractValidationProvider
{
    public function __construct(
        public readonly array $rules = [],
        public readonly array $messages = [],
        public readonly array $attributes = [],
    ) {
    }

    /*
     * --------------------------------
     * Core
     * --------------------------------
     */

    public function rules(): array
    {
        return $this->rules;
    }

    public function messages(): array
    {
        return $this->messages;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }
}
