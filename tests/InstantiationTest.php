<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Facades\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AuthorValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\BookValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\ArrayValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\CustomValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\ExcludeAttributesValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\MapAttributesValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

it('instantiates object hierarchies consistently', function () {

    $customRules = [
        'books' => ['required', 'array', 'min:1', 'max:2',],
    ];

    $customMessages = [
        'books.required' => 'Provide :attribute'
    ];

    $customAttributes = [
        'books' => 'BOOKS'
    ];

    $manualInstantiation = new NestedValidationProvider(
        'author',
        new AggregateValidationProvider(
            new AuthorValidationProvider(),
            new CustomValidationProvider($customRules, $customMessages, $customAttributes),
            new ArrayValidationProvider(
                'books',
                new MapAttributesValidationProvider(
                    ['title' => 'real_title'],
                    new ExcludeAttributesValidationProvider(
                        ['description'],
                        new BookValidationProvider()
                    )
                )
            )
        )
    );

    $facadeInstantiation = ValidationProvider::make([
        'author' => [
            AuthorValidationProvider::class,
            new CustomValidationProvider($customRules, $customMessages, $customAttributes),
            new ArrayValidationProvider(
                'books',
                new MapAttributesValidationProvider(
                    ['title' => 'real_title'],
                    new ExcludeAttributesValidationProvider(
                        ['description'],
                        new BookValidationProvider()
                    )
                )
            ),
        ],
    ]);

    $fluentInstantiationObjects = (new BookValidationProvider())
        ->exclude(['description'])
        ->map(['title' => 'real_title'])
        ->nestedArray('books')
        ->with(new CustomValidationProvider($customRules, $customMessages, $customAttributes))
        ->with(new AuthorValidationProvider())
        ->nested('author');

    $fluentInstantiationClassString = (new BookValidationProvider())
        ->exclude(['description'])
        ->map(['title' => 'real_title'])
        ->nestedArray('books')
        ->with(new CustomValidationProvider($customRules, $customMessages, $customAttributes))
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
        'author.name'               => ['required'],
        'author.books'              => ['required', 'array', 'min:1', 'max:2',],
        'author.books.*.real_title' => ['required'],
    ]);
    expect($manualInstantiation->messages())->toEqual([
        'author.books.required'              => 'Provide :attribute',
        'author.books.*.real_title.required' => ':attribute IS REQUIRED',
    ]);
    expect($manualInstantiation->attributes())->toEqual([
        'author.books'              => 'BOOKS',
        'author.books.*.real_title' => 'TITLE',
    ]);

});
