<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Tests\ValidationProviders;

use IndexZer0\LaravelValidationProvider\ValidationProviders\AbstractValidationProvider;

class BookValidationProvider extends AbstractValidationProvider
{
    protected array $rules = [
        'title'       => ['required',],
        'description' => ['required',],
    ];

    protected array $messages = [
        'title.required' => ':attribute IS REQUIRED',
    ];

    protected array $attributes = [
        'title' => 'TITLE',
    ];

}
