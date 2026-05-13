<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSetupAccomplishmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'encoder']);
    }

    public function rules(): array
    {
        return [
            'year'                => ['required', 'integer', 'min:2000', 'max:2099'],
            'target_num_projects' => ['required', 'integer', 'min:0'],
            'target_ifund_amount' => ['required', 'numeric', 'min:0'],
            'target_gross_sales'  => ['required', 'numeric', 'min:0'],
            'target_employment'   => ['required', 'integer', 'min:0'],
            'target_trainings'    => ['required', 'integer', 'min:0'],
            'actual_num_projects' => ['required', 'integer', 'min:0'],
            'actual_ifund_amount' => ['required', 'numeric', 'min:0'],
            'actual_gross_sales'  => ['required', 'numeric', 'min:0'],
            'actual_employment'   => ['required', 'integer', 'min:0'],
            'actual_trainings'    => ['required', 'integer', 'min:0'],
        ];
    }
}
