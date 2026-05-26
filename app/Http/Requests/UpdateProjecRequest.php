<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjecRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name'=>'required',
            'description'=>'required',
            'document' => 'nullable|array',
            'document.*' => 'file|mimes:pdf,doc,docx|max:10240',
            'deleted_document_ids' => 'nullable|array',
            'deleted_document_ids.*' => 'integer|exists:project_documents,id',
        ];
    }
}
