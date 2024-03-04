<?php

declare(strict_types=1);

use IndexZer0\LaravelValidationProvider\Facades\ValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\AuthorValidationProvider;
use IndexZer0\LaravelValidationProvider\Tests\ValidationProviders\BookValidationProvider;
use IndexZer0\LaravelValidationProvider\ValidationProviders\CustomValidationProvider;

it('can make from config', function (mixed $make_config, array $expected_rules) {

    $validationProvider = ValidationProvider::make($make_config);

    expect($validationProvider->rules())->toBe($expected_rules);

})->with([
    'single validation provider | class string' => [
        'make_config' => AuthorValidationProvider::class,
        'expected_rules' => [
            'name' => ['required']
        ]
    ],

    'single validation provider | object' => [
        'make_config' => new AuthorValidationProvider(),
        'expected_rules' => [
            'name' => ['required']
        ]
    ],

    'two validation provider | expect aggregate' => [
        'make_config' => [
            AuthorValidationProvider::class,
            BookValidationProvider::class,
        ],
        'expected_rules' => [
            'name' => ['required'],
            'title' => ['required']
        ]
    ],

    'nested with one child' => [
        'make_config' => [
            'author' => AuthorValidationProvider::class,
        ],
        'expected_rules' => [
            'author.name' => ['required'],
        ]
    ],

    'expect aggregate with two nested' => [
        'make_config' => [
            'author' => AuthorValidationProvider::class,
            'book' => BookValidationProvider::class
        ],
        'expected_rules' => [
            'author.name' => ['required'],
            'book.title' => ['required'],
        ]
    ],

    'nested with aggregate' => [
        'make_config' => [
            'author' => [
                AuthorValidationProvider::class,
                'book' => BookValidationProvider::class
            ],
        ],
        'expected_rules' => [
            'author.name' => ['required'],
            'author.book.title' => ['required'],
        ]
    ],

    'nested with aggregate with *' => [
        'make_config' => [
            'author' => [
                AuthorValidationProvider::class,
                'books' => [
                    '*' => [
                        BookValidationProvider::class
                    ]
                ]
            ],
        ],
        'expected_rules' => [
            'author.name' => ['required'],
            'author.books.*.title' => ['required'],
        ]
    ],
    'nested with aggregate with * sd' => [
        'make_config' => [
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
        ],
        'expected_rules' => [
            'author.name' => ['required'],
            'author.books' => ['required', 'array', 'min:1', 'max:2',],
            'author.books.*.title' => ['required'],
        ]
    ],
]);


it('fails make from config', function (mixed $make_config, string $expected_exception_message) {

    try {

        ValidationProvider::make($make_config);
        $this->fail('Should have failed');

    } catch (Throwable $t) {
        expect($t->getMessage())->toBe($expected_exception_message);
    }

})->with([
    'single validation provider | class string' => [
        'make_config' => stdClass::class,
        'expected_exception_message' => 'Class must be a ValidationProvider'
    ],

    'single validation provider | nested with class string' => [
        'make_config' => [
            'key' => stdClass::class
        ],
        'expected_exception_message' => 'Class must be a ValidationProvider',
    ],
]);
