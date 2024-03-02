<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Contracts;

interface ValidationProvider
{
    public function rules(): array;

    public function messages(): array;

    public function attributes(): array;

    public function dependentField(string $field): string;

    public function prependNestedKey(string $nestedKey, bool $increaseLevel): void;
}
