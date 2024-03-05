<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AddressValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\ArrayValidationProvider;

it('nests validation rules', function () {
    $validationProvider = new ArrayValidationProvider(
        'addresses',
        new AddressValidationProvider(),
    );

    expect($validationProvider->rules())->toBe([
        'addresses.*.post_code'           => [
            'required',
            'string',
            'min:1',
            'required_if:addresses.*.street,something'
        ],
        'addresses.*.street'              => [
            'required',
            'string',
            'min:1',
        ],
        'addresses.*.home_phone_number'   => [
            'required',
            'string',
            'min:1',
        ],
    ]);
});
