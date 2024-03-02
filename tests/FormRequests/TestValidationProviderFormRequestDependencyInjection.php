<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Tests\FormRequests;

use IndexZer0\LaravelValidationProvider\Contracts\ValidationProvider;
use IndexZer0\LaravelValidationProvider\FormRequests\ValidationProviderFormRequest;

class TestValidationProviderFormRequestDependencyInjection extends ValidationProviderFormRequest
{
    public function __construct(ValidationProvider $validationProvider)
    {
        $this->validationProvider = $validationProvider;
    }
}
