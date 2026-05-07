<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'encoder']);
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'in:GAA,financial_plan,work_plan,MOA,other'],
            'file'          => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ];
    }
}
