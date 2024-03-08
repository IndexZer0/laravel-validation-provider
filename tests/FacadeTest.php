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
        'make_config'    => AuthorValidationProvider::class,
        'expected_rules' => [
            'name' => ['required']
        ]
    ],

    'single validation provider | object' => [
        'make_config'    => new AuthorValidationProvider(),
        'expected_rules' => [
            'name' => ['required']
        ]
    ],

    'two validation providers | class strings' => [
        'make_config'    => [
            AuthorValidationProvider::class,
            BookValidationProvider::class,
        ],
        'expected_rules' => [
            'name'        => ['required'],
            'title'       => ['required'],
            'description' => ['required'],
        ]
    ],

    'two validation providers | objects' => [
        'make_config'    => [
            new AuthorValidationProvider(),
            new BookValidationProvider(),
        ],
        'expected_rules' => [
            'name'        => ['required'],
            'title'       => ['required'],
            'description' => ['required'],
        ]
    ],

    'nested with one child' => [
        'make_config'    => [
            'author' => AuthorValidationProvider::class,
        ],
        'expected_rules' => [
            'author.name' => ['required'],
        ]
    ],

    'aggregate with two nested' => [
        'make_config'    => [
            'author' => AuthorValidationProvider::class,
            'book'   => BookValidationProvider::class
        ],
        'expected_rules' => [
            'author.name'      => ['required'],
            'book.title'       => ['required'],
            'book.description' => ['required'],
        ]
    ],

    'nested with aggregate' => [
        'make_config'    => [
            'author' => [
                AuthorValidationProvider::class,
                'book' => BookValidationProvider::class
            ],
        ],
        'expected_rules' => [
            'author.name'             => ['required'],
            'author.book.title'       => ['required'],
            'author.book.description' => ['required'],
        ]
    ],

    'nested with aggregate (using * astrix)'            => [
        'make_config'    => [
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
            'author.name'                => ['required'],
            'author.books.*.title'       => ['required'],
            'author.books.*.description' => ['required'],
        ]
    ],
    'nested with aggregate (using * astrix) and custom' => [
        'make_config'    => [
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
            'author.name'                => ['required'],
            'author.books'               => ['required', 'array', 'min:1', 'max:2',],
            'author.books.*.title'       => ['required'],
            'author.books.*.description' => ['required'],
        ]
    ],
]);


it('fails make from config', function (mixed $make_config, string $expected_exception_message) {

    try {

        ValidationProvider::make($make_config);
        $this->fail('Should have failed');

    } catch (IndexZer0\LaravelValidationProvider\Exceptions\InvalidArgumentException $invalidArgumentException) {
        expect($invalidArgumentException->getMessage())->toBe($expected_exception_message);
    }

})->with([
    'single validation provider | class string' => [
        'make_config'                => stdClass::class,
        'expected_exception_message' => 'Class must be a ValidationProvider'
    ],

    'single validation provider | nested with class string' => [
        'make_config'                => [
            'key' => stdClass::class
        ],
        'expected_exception_message' => 'Class must be a ValidationProvider',
    ],

    'empty array' => [
        'make_config'                => [],
        'expected_exception_message' => 'Empty array provided',
    ],

    'empty array in nested' => [
        'make_config'                => [
            'something' => []
        ],
        'expected_exception_message' => 'Empty array provided',
    ]
]);
