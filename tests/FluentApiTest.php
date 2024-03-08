<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AuthorValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\BookValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\ArrayValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\ExcludeAttributesValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\MapAttributesValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

it('can nest | nested()', function () {

    $validationProvider = (new AuthorValidationProvider())
        ->nested('author');

    expect($validationProvider)->toBeInstanceOf(NestedValidationProvider::class);
    expect($validationProvider->validationProvider)->toBeInstanceOf(AuthorValidationProvider::class);

    expect($validationProvider->rules())->toEqual([
        'author.name' => ['required',],
    ]);

});

it('can array nest | nestedArray()', function () {

    $validationProvider = (new AuthorValidationProvider())
        ->nestedArray('authors');

    expect($validationProvider)->toBeInstanceOf(ArrayValidationProvider::class);
    expect($validationProvider->validationProvider)->toBeInstanceOf(AuthorValidationProvider::class);

    expect($validationProvider->rules())->toEqual([
        'authors.*.name' => ['required',],
    ]);

});

it('can aggregate | with()', function () {

    $validationProvider = (new AuthorValidationProvider())
        ->with(BookValidationProvider::class);

    expect($validationProvider)->toBeInstanceOf(AggregateValidationProvider::class);
    $validationProviders = $validationProvider->validationProviders;
    expect($validationProviders[0])->toBeInstanceOf(BookValidationProvider::class);
    expect($validationProviders[1])->toBeInstanceOf(AuthorValidationProvider::class);

    expect($validationProvider->rules())->toEqual([
        'title'       => ['required',],
        'description' => ['required',],
        'name'        => ['required',],
    ]);

});

it('can exclude | exclude()', function () {

    $validationProvider = (new AuthorValidationProvider())
        ->exclude(['name']);

    expect($validationProvider)->toBeInstanceOf(ExcludeAttributesValidationProvider::class);
    expect($validationProvider->validationProvider)->toBeInstanceOf(AuthorValidationProvider::class);

    expect($validationProvider->rules())->toEqual([]);

});

it('can map | map()', function () {

    $validationProvider = (new AuthorValidationProvider())
        ->map(['name' => 'author_name']);

    expect($validationProvider)->toBeInstanceOf(MapAttributesValidationProvider::class);
    expect($validationProvider->validationProvider)->toBeInstanceOf(AuthorValidationProvider::class);

    expect($validationProvider->rules())->toEqual([
        'author_name' => ['required']
    ]);

});

it('fails when passing a non ValidationProvider class as string', function (string $fqcn) {

    try {
        (new AuthorValidationProvider())
            ->with($fqcn);

        $this->fail('Should have failed');

    } catch (IndexZer0\LaravelValidationProvider\Exceptions\InvalidArgumentException $invalidArgumentException) {
        expect($invalidArgumentException->getMessage())->toBe('Class must be a ValidationProvider');
    }

})->with([
    'non ValidationProvider class | stdClass' => [
        'fqcn' => stdClass::class,
    ],

    'non existing class' => [
        'fqcn' => 'random garbage',
    ]
]);
