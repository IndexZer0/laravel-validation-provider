<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Facades\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AuthorValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\BookValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\CustomValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

it('instantiates object hierarchies consistently', function () {

    $manualInstantiation = (new NestedValidationProvider(
        'author',
        (new AggregateValidationProvider(
            new AuthorValidationProvider(),
            new CustomValidationProvider([
                'books' => ['required', 'array', 'min:1', 'max:2',]
            ]),
            new NestedValidationProvider(
                'books',
                new NestedValidationProvider(
                    '*',
                    new BookValidationProvider()
                )
            )
        ))
    ));

    $facadeInstantiation = ValidationProvider::make([
        'author' => [
            AuthorValidationProvider::class,
            new CustomValidationProvider([
                'books' => ['required', 'array', 'min:1', 'max:2',]
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
            'books' => ['required', 'array', 'min:1', 'max:2',]
        ]))
        ->with(new AuthorValidationProvider())
        ->nested('author');

    $fluentInstantiationClassString = (new BookValidationProvider())
        ->nestedArray('books')
        ->with(new CustomValidationProvider([
            'books' => ['required', 'array', 'min:1', 'max:2',]
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

    expect($manualInstantiation->rules())->toEqual([
        'author.name'          => ['required'],
        'author.books'         => ['required', 'array', 'min:1', 'max:2',],
        'author.books.*.title' => ['required'],
    ]);

});
