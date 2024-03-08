<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AddressValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\ExcludeAttributesValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

it('can exclude rules, messages, and attributes', function () {

    $validationProvider = new ExcludeAttributesValidationProvider(
        ['address.post_code', 'address.street'],
        new NestedValidationProvider(
            'address',
            new AddressValidationProvider(),
        )
    );

    expect($validationProvider->rules())->toBe([
        'address.home_phone_number'   => [
            'required',
            'string',
            'min:1',
        ],
    ]);

    expect($validationProvider->messages())->toBe([
        'address.home_phone_number.required' => 'HOME PHONE NUMBER is required',
    ]);

    expect($validationProvider->attributes())->toBe([
        'address.home_phone_number' => 'HOME PHONE NUMBER',
    ]);
});
