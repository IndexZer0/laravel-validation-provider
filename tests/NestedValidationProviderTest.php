<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AddressValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\ContactValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

it('nests validation rules', function () {
    $validationProvider = new NestedValidationProvider(
        'address',
        new AddressValidationProvider(),
    );

    expect($validationProvider->rules())->toBe([
        'address.post_code'           => [
            'required',
            'string',
            'min:1',
            'required_if:address.street,something'
        ],
        'address.street'              => [
            'required',
            'string',
            'min:1',
        ],
        'address.home_phone_number'   => [
            'required',
            'string',
            'min:1',
        ],
    ]);
});

it('nests validation messages', function () {
    $validationProvider = new NestedValidationProvider(
        'address',
        new AddressValidationProvider(),
    );

    expect($validationProvider->messages())->toBe([
        'address.post_code.required' => 'POST CODE is required',
        'address.home_phone_number.required' => 'HOME PHONE NUMBER is required',
    ]);
});

it('nests validation attributes', function () {
    $validationProvider = new NestedValidationProvider(
        'address',
        new AddressValidationProvider()
    );

    expect($validationProvider->attributes())->toBe([
        'address.street' => 'STREET',
        'address.home_phone_number' => 'HOME PHONE NUMBER',
    ]);
});

it('nests validation rules | two nests', function () {

    $validationProvider = new NestedValidationProvider(
        'user',
        new NestedValidationProvider(
            'address',
            new AddressValidationProvider()
        ),
    );

    expect($validationProvider->rules())->toBe([
        'user.address.post_code'           => [
            'required',
            'string',
            'min:1',
            'required_if:user.address.street,something'
        ],
        'user.address.street'              => [
            'required',
            'string',
            'min:1',
        ],
        'user.address.home_phone_number'   => [
            'required',
            'string',
            'min:1',
        ],
    ]);
});

it('nests validation rules with aggregate', function () {

    $validationProvider = new NestedValidationProvider(
        'user',
        new AggregateValidationProvider(
            new NestedValidationProvider(
                'address',
                new AddressValidationProvider()
            ),
            new ContactValidationProvider()
        )
    );

    expect($validationProvider->rules())->toBe([
        'user.address.post_code'           => [
            'required',
            'string',
            'min:1',
            'required_if:user.address.street,something'
        ],
        'user.address.street'              => [
            'required',
            'string',
            'min:1',
        ],
        'user.address.home_phone_number'   => [
            'required',
            'string',
            'min:1',
        ],
        'user.email'               => [
            'required',
            'string',
            'min:1',
        ],
        'user.home_phone_number'   => [
            'required',
            'string',
            'min:1',
        ],
        'user.mobile_phone_number' => [
            'required',
            'string',
            'min:1',
        ],

    ]);
});

it('nests validation rules | can function as ArrayValidationProvider by using array dot astrix notation', function () {
    $validationProvider = new NestedValidationProvider(
        'addresses.*',
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
