<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\ValidationProviders\AbstractValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\AggregateValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\MapAttributesValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\NestedValidationProvider;

class BookForMapTest extends AbstractValidationProvider
{
    protected array $rules = [
        'title' => ['required',],
    ];

    protected array $messages = [
        'title.required' => ':attribute IS REQUIRED',
    ];

    protected array $attributes = [
        'title' => 'TITLE',
    ];
}

it('can map rules, messages, and attributes', function () {

    $validationProvider = new MapAttributesValidationProvider(
        [
            'book_title_4'        => 'book_title_5',
            'book_2.book_title_2' => 'book_title_6'
        ],
        new AggregateValidationProvider(
            new NestedValidationProvider(
                'book_1',
                new MapAttributesValidationProvider(
                    ['title' => 'book_title_1'],
                    new BookForMapTest()
                )
            ),
            new NestedValidationProvider(
                'book_2',
                new MapAttributesValidationProvider(
                    ['title' => 'book_title_2'],
                    new BookForMapTest()
                )
            ),
            new MapAttributesValidationProvider(
                ['title' => 'book_title_3'],
                new BookForMapTest()
            ),
            new MapAttributesValidationProvider(
                ['title' => 'book_title_4'],
                new BookForMapTest()
            ),
        )
    );

    expect($validationProvider->rules())->toBe([
        'book_1.book_title_1' => ['required',],
        'book_title_6'        => ['required',],
        'book_title_3'        => ['required',],
        'book_title_5'        => ['required',]
    ]);

    expect($validationProvider->messages())->toBe([
        'book_1.book_title_1.required' => ':attribute IS REQUIRED',
        'book_title_6.required'        => ':attribute IS REQUIRED',
        'book_title_3.required'        => ':attribute IS REQUIRED',
        'book_title_5.required'        => ':attribute IS REQUIRED',
    ]);

    expect($validationProvider->attributes())->toBe([
        'book_1.book_title_1' => 'TITLE',
        'book_title_6'        => 'TITLE',
        'book_title_3'        => 'TITLE',
        'book_title_5'        => 'TITLE',
    ]);
});
