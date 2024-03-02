<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Tests\ValidationProviders;

use IndexZer0\LaravelValidationProvider\ValidationProviders\AbstractValidationProvider;

class AddressValidationProvider extends AbstractValidationProvider
{
    public function __construct()
    {
    }

    public function rules(): array
    {
        return [
            'post_code' => ['required', 'string', 'min:1', "required_if:{$this->dependentField('street')},something"],
            'street' => ['required', 'string', 'min:1'],
            'home_phone_number' => ['required', 'string', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'post_code.required' => 'POST CODE is required',
            'home_phone_number.required' => 'HOME PHONE NUMBER is required',
        ];
    }

    public function attributes(): array
    {
        return [
            'street' => 'STREET',
            'home_phone_number' => 'HOME PHONE NUMBER',
        ];
    }
}
