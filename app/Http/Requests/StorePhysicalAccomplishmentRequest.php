<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhysicalAccomplishmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'encoder']);
    }

    public function rules(): array
    {
        return [
            'year'               => ['required', 'integer', 'min:2000', 'max:2099'],
            'quarter'            => ['required', 'integer', 'between:1,4'],
            'month'              => ['required', 'integer', 'between:1,12'],
            'indicator_name'     => ['required', 'string', 'max:255'],
            'target_value'       => ['required', 'numeric', 'min:0'],
            'accomplished_value' => ['required', 'numeric', 'min:0'],
        ];
    }
}
