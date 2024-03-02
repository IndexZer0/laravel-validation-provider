<?php

declare(strict_types=1);

namespace IndexZer0\LaravelValidationProvider\FormRequests;

use Illuminate\Foundation\Http\FormRequest;
use IndexZer0\LaravelValidationProvider\Traits\HasValidationProvider;

class ValidationProviderFormRequest extends FormRequest
{
    use HasValidationProvider;
}
