<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Traits;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

trait HasValidationProvider
{
    protected ValidationProvider $validationProvider;

    public function rules(): array
    {
        return $this->validationProvider->rules();
    }

    public function messages(): array
    {
        return $this->validationProvider->messages();
    }

    public function attributes(): array
    {
        return $this->validationProvider->attributes();
    }
}
