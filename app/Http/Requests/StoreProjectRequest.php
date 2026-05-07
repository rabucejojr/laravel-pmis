<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'encoder']);
    }

    public function rules(): array
    {
        return [
            'program_id'            => ['required', 'exists:programs,id'],
            'title'                 => ['required', 'string', 'max:255'],
            'description'           => ['nullable', 'string'],
            'implementing_agency'   => ['required', 'string', 'max:255'],
            'location'              => ['nullable', 'string', 'max:255'],
            'start_date'            => ['required', 'date'],
            'end_date'              => ['required', 'date', 'after:start_date'],
            'total_approved_budget' => ['required', 'numeric', 'min:0'],
            'status'                => ['required', 'in:active,completed,suspended,terminated'],
        ];
    }
}
