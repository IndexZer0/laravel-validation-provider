<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\ValidationProviders;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;

abstract class AbstractValidationProvider implements ValidationProvider
{
    protected array $nestedKey = [];

    protected int $level = 1;

    public function dependentField(string $field): string
    {
        $nestedKey = $this->getNestedKeyDotNotation();
        if ($nestedKey === '') {
            return $field;
        }
        return join('.', [$nestedKey, $field]);
    }

    public function prependNestedKey(string $nestedKey, bool $increaseLevel): void
    {
        if ($increaseLevel) {
            $this->increaseLevel();
        }
        array_unshift($this->nestedKey, $nestedKey);
    }

    protected function getNestedKeyDotNotation(): string
    {
        return join('.', $this->nestedKey);
    }

    public function increaseLevel(): void
    {
        $this->level++;
    }
}
