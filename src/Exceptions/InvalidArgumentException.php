<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Exceptions;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProviderExceptionInterface;
use InvalidArgumentException as BaseInvalidArgumentException;

class InvalidArgumentException extends BaseInvalidArgumentException implements ValidationProviderExceptionInterface
{
}
