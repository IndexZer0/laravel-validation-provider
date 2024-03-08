<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

class CustomValidationProvider extends AbstractValidationProvider
{
    public function __construct(
        protected array $rules = [],
        protected array $messages = [],
        protected array $attributes = [],
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
