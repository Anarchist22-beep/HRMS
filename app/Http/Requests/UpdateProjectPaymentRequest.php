<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectPaymentRequest extends ApiRequest
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
        'project_id.required'               => 'Project ID is required.',
        'project_id.exists'                 => 'Project not found.',
        'payments.required'                 => 'At least one payment is required.',
        'payments.min'                      => 'At least one payment is required.',
        'payments.*.amount.required'        => 'Amount is required for all rows.',
        'payments.*.amount.numeric'         => 'Amount must be a valid number.',
        'payments.*.amount.min'             => 'Amount must be 0 or greater.',
        'payments.*.amount_paid.required'   => 'Amount paid status is required.',
        'payments.*.amount_paid.boolean'    => 'Amount paid must be true or false.',
        'payments.*.amount_received.required' => 'Amount received status is required.',
        'payments.*.amount_received.boolean'  => 'Amount received must be true or false.',
    ];
    }
}
