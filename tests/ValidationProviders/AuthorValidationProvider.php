<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Tests\ValidationProviders;

use IndexZer0\LaravelValidationProvider\ValidationProviders\AbstractValidationProvider;

class AuthorValidationProvider extends AbstractValidationProvider
{
    protected array $rules = [
        'name' => ['required',],
    ];
}
