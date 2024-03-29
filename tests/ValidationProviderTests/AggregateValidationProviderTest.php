<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AddressValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\ContactValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;

it('aggregates validation rules', function () {
    $validationProvider = new AggregateValidationProvider(
        new AddressValidationProvider(),
        new ContactValidationProvider()
    );

    expect($validationProvider->rules())->toBe([
        'post_code'           => [
            'required',
            'string',
            'min:1',
            'required_if:street,something'
        ],
        'street'              => [
            'required',
            'string',
            'min:1',
        ],
        'home_phone_number'   => [
            'required',
            'string',
            'min:1',
            'required',
            'string',
            'min:1',
        ],
        'email'               => [
            'required',
            'string',
            'min:1',
        ],
        'mobile_phone_number' => [
            'required',
            'string',
            'min:1',
        ]
    ]);
});

it('aggregates validation messages', function () {
    $validationProvider = new AggregateValidationProvider(
        new AddressValidationProvider(),
        new ContactValidationProvider()
    );

    expect($validationProvider->messages())->toBe([
        'post_code.required'         => 'POST CODE is required',
        'home_phone_number.required' => 'HOME NUMBER is required',
        'email.required'             => 'EMAIL is required',
    ]);
});

it('aggregates validation attributes', function () {
    $validationProvider = new AggregateValidationProvider(
        new AddressValidationProvider(),
        new ContactValidationProvider()
    );

    expect($validationProvider->attributes())->toBe([
        'street'            => 'STREET',
        'home_phone_number' => 'HOME NUMBER',
        'phone_number'      => 'PHONE NUMBER',
    ]);
});
