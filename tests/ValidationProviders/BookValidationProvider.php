<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Tests\ValidationProviders;

use IndexZer0\LaravelValidationProvider\ValidationProviders\AbstractValidationProvider;

class BookValidationProvider extends AbstractValidationProvider
{
    public function rules(): array
    {
        return [
            'title' => ['required',],
            'description' => ['required',],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => ':attribute IS REQUIRED',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'TITLE',
        ];
    }
}
