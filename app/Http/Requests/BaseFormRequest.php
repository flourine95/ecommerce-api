<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();

        throw new HttpResponseException(
            ApiResponse::validationError($errors, 'The given data is invalid')
        );
    }
}
