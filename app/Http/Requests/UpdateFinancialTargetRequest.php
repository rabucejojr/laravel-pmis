<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinancialTargetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'encoder']);
    }

    public function rules(): array
    {
        return [
            'year'             => ['required', 'integer', 'min:2000', 'max:2099'],
            'quarter'          => ['required', 'integer', 'between:1,4'],
            'month'            => ['required', 'integer', 'between:1,12'],
            'line_item'        => ['required', 'string', 'max:255'],
            'target_amount'    => ['required', 'numeric', 'min:0'],
            'obligated_amount' => ['required', 'numeric', 'min:0'],
            'disbursed_amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
