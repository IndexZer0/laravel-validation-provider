<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Facades\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AuthorValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\BookValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\CustomValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

it('instantiates object hierarchies consistently', function () {

    $manualInstantiation = new NestedValidationProvider(
        'author',
        new AggregateValidationProvider(
            new AuthorValidationProvider(),
            new CustomValidationProvider([
                'books' => ['required', 'array', 'min:1', 'max:2',],
            ], [
                'books.required' => 'Provide :attribute'
            ], [
                'books' => 'BOOKS'
            ]),
            new NestedValidationProvider(
                'books',
                new NestedValidationProvider(
                    '*',
                    new BookValidationProvider()
                )
            )
        )
    );

    $facadeInstantiation = ValidationProvider::make([
        'author' => [
            AuthorValidationProvider::class,
            new CustomValidationProvider([
                'books' => ['required', 'array', 'min:1', 'max:2',],
            ], [
                'books.required' => 'Provide :attribute'
            ], [
                'books' => 'BOOKS'
            ]),
            'books' => [
                '*' => [
                    BookValidationProvider::class,
                ],
            ]
        ],
    ]);

    $fluentInstantiationObjects = (new BookValidationProvider())
        ->nestedArray('books')
        ->with(new CustomValidationProvider([
            'books' => ['required', 'array', 'min:1', 'max:2',],
        ], [
            'books.required' => 'Provide :attribute'
        ], [
            'books' => 'BOOKS'
        ]))
        ->with(new AuthorValidationProvider())
        ->nested('author');

    $fluentInstantiationClassString = (new BookValidationProvider())
        ->nestedArray('books')
        ->with(new CustomValidationProvider([
            'books' => ['required', 'array', 'min:1', 'max:2',],
        ], [
            'books.required' => 'Provide :attribute'
        ], [
            'books' => 'BOOKS'
        ]))
        ->with(AuthorValidationProvider::class)
        ->nested('author');

    // Expect object hierarchies to be equal
    expect($manualInstantiation)->toEqual($facadeInstantiation);
    expect($manualInstantiation)->toEqual($fluentInstantiationObjects);
    expect($manualInstantiation)->toEqual($fluentInstantiationClassString);

    // Expect rules to be equal
    expect($manualInstantiation->rules())->toEqual($facadeInstantiation->rules());
    expect($manualInstantiation->rules())->toEqual($fluentInstantiationObjects->rules());
    expect($manualInstantiation->rules())->toEqual($fluentInstantiationClassString->rules());

    // Expect messages to be equal
    expect($manualInstantiation->messages())->toEqual($facadeInstantiation->messages());
    expect($manualInstantiation->messages())->toEqual($fluentInstantiationObjects->messages());
    expect($manualInstantiation->messages())->toEqual($fluentInstantiationClassString->messages());

    // Expect attributes to be equal
    expect($manualInstantiation->attributes())->toEqual($facadeInstantiation->attributes());
    expect($manualInstantiation->attributes())->toEqual($fluentInstantiationObjects->attributes());
    expect($manualInstantiation->attributes())->toEqual($fluentInstantiationClassString->attributes());

    expect($manualInstantiation->rules())->toEqual([
        'author.name'          => ['required'],
        'author.books'         => ['required', 'array', 'min:1', 'max:2',],
        'author.books.*.title' => ['required'],
    ]);
    expect($manualInstantiation->messages())->toEqual([
        'author.books.required' => 'Provide :attribute',
    ]);
    expect($manualInstantiation->attributes())->toEqual([
        'author.books' => 'BOOKS',
    ]);

});
