<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\FormRequests\TestValidationProviderFormRequestDependencyInjection;
use IndexZer0\LaravelValidationProvider\Tests\FormRequests\TestValidationProviderFormRequestPrepareHook;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AddressValidationProvider;
use Illuminate\Support\Facades\Route;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\ContactValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

use function Pest\Laravel\json;
use function Pest\Laravel\swap;

it('validates in a form request | prepare hook', function (
    ValidationProvider $validation_provider,
    array $request_data,
    bool $expect_fail_validation,
    ?array $expected_validation_messages,
) {

    Route::post('test', function (TestValidationProviderFormRequestPrepareHook $request) {
        return response()->json($request->validated());
    });

    swap('ValidationProviderForFormRequestTest', $validation_provider);

    $response = json('post', 'test', $request_data);

    if ($expect_fail_validation) {
        $response->assertJsonValidationErrors($expected_validation_messages);
    } else {
        $response->assertExactJson($request_data);
    }

})->with([
    'address | fail' => [
        'validation_provider' => new AddressValidationProvider(),
        'request_data' => [],
        'expect_fail_validation' => true,
        'expected_validation_messages' => [
            'post_code' => [
                "POST CODE is required",
            ],
            'street' => [
                'The STREET field is required.',
            ],
            'home_phone_number' => [
                'HOME PHONE NUMBER is required',
            ],
        ],
    ],
    'address | success' => [
        'validation_provider' => new AddressValidationProvider(),
        'request_data' => [
            'post_code' => 'hi',
            'street' => 'hi',
            'home_phone_number' => 'hi'
        ],
        'expect_fail_validation' => false,
        'expected_validation_messages' => null,
    ],
    'nested contact | fail' => [
        'validation_provider' => new NestedValidationProvider(
            'user',
            new ContactValidationProvider()
        ),
        'request_data' => [],
        'expect_fail_validation' => true,
        'expected_validation_messages' => [
            'user.email' => [
                "EMAIL is required",
            ],
            'user.home_phone_number' => [
                'HOME NUMBER is required',
            ],
            'user.mobile_phone_number' => [
                'The user.mobile phone number field is required.',
            ],
        ],
    ],
    'nested contact | success' => [
        'validation_provider' => new NestedValidationProvider(
            'user',
            new ContactValidationProvider()
        ),
        'request_data' => [
            'user' => [
                'email' => 'hi',
                'home_phone_number' => 'hi',
                'mobile_phone_number' => 'hi',
            ]
        ],
        'expect_fail_validation' => false,
        'expected_validation_messages' => null,
    ],
]);

it('validates in a form request | dependency injection', function (
    ValidationProvider $validation_provider,
    array $request_data,
    bool $expect_fail_validation,
    ?array $expected_validation_messages,
) {

    Route::post('test', function (TestValidationProviderFormRequestDependencyInjection $request) {
        return response()->json($request->validated());
    });

    app()->when(TestValidationProviderFormRequestDependencyInjection::class)
        ->needs(ValidationProvider::class)
        ->give(function () use ($validation_provider) {
            return $validation_provider;
        });

    $response = json('post', 'test', $request_data);

    if ($expect_fail_validation) {
        $response->assertJsonValidationErrors($expected_validation_messages);
    } else {
        $response->assertExactJson($request_data);
    }

})->with([
    'address | fail' => [
        'validation_provider' => new AddressValidationProvider(),
        'request_data' => [],
        'expect_fail_validation' => true,
        'expected_validation_messages' => [
            'post_code' => [
                "POST CODE is required",
            ],
            'street' => [
                'The STREET field is required.',
            ],
            'home_phone_number' => [
                'HOME PHONE NUMBER is required',
            ],
        ],
    ],
    'address | success' => [
        'validation_provider' => new AddressValidationProvider(),
        'request_data' => [
            'post_code' => 'hi',
            'street' => 'hi',
            'home_phone_number' => 'hi'
        ],
        'expect_fail_validation' => false,
        'expected_validation_messages' => null,
    ],
    'nested contact | fail' => [
        'validation_provider' => new NestedValidationProvider(
            'user',
            new ContactValidationProvider()
        ),
        'request_data' => [],
        'expect_fail_validation' => true,
        'expected_validation_messages' => [
            'user.email' => [
                "EMAIL is required",
            ],
            'user.home_phone_number' => [
                'HOME NUMBER is required',
            ],
            'user.mobile_phone_number' => [
                'The user.mobile phone number field is required.',
            ],
        ],
    ],
    'nested contact | success' => [
        'validation_provider' => new NestedValidationProvider(
            'user',
            new ContactValidationProvider()
        ),
        'request_data' => [
            'user' => [
                'email' => 'hi',
                'home_phone_number' => 'hi',
                'mobile_phone_number' => 'hi',
            ]
        ],
        'expect_fail_validation' => false,
        'expected_validation_messages' => null,
    ],
]);
