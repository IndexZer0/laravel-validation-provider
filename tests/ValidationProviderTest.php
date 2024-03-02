<?php

declare(strict_types=1);

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AddressValidationProvider;

it('can create validator', function () {
    $validationProvider = new AddressValidationProvider();

    $validator = $validationProvider->createValidator(['some' => 'data']);

    expect($validator)->toBeInstanceOf(Validator::class);
});

it('can validate', function () {
    $validationProvider = new AddressValidationProvider();

    try {

        $validationProvider->validate(['some' => 'data']);
        $this->fail('Should have thrown exception');

    } catch (ValidationException $ve) {

        expect($ve->errors())->toBe([
            'post_code'         => [
                'POST CODE is required',
            ],
            'street'            => [
                'The STREET field is required.',
            ],
            'home_phone_number' => [
                'HOME PHONE NUMBER is required',
            ],
        ]);
    }
});
