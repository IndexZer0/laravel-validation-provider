<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Tests\ValidationProviders;

use IndexZer0\LaravelValidationProvider\ValidationProviders\AbstractValidationProvider;

class ContactValidationProvider extends AbstractValidationProvider
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'min:1'],
            'home_phone_number' => ['required', 'string', 'min:1'],
            'mobile_phone_number' => ['required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'EMAIL is required',
            'home_phone_number.required' => 'HOME NUMBER is required',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone_number' => 'PHONE NUMBER',
            'home_phone_number' => 'HOME NUMBER',
        ];
    }
}
