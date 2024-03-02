<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\Tests\FormRequests;

use IndexZer0\LaravelValidationProvider\FormRequests\ValidationProviderFormRequest;

class TestValidationProviderFormRequestPrepareHook extends ValidationProviderFormRequest
{
    public function prepareForValidation()
    {
        $this->validationProvider = app('ValidationProviderForFormRequestTest');
    }
}
