<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssignShiftRequest extends ApiRequest
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
            'department_id'=>'required|exists:departments,id',
            'user_id'=>'required|exists:users,id',
            'shift_id'=>'required|exists:shifts,id',
            'start_date'=>'required|date',
            'end_date'=>'nullable|date',
            'monday'=>'required|boolean',
            'tuesday'=>'required|boolean',
            'wednesday'=>'required|boolean',
            'thursday'=>'required|boolean',
            'friday'=>'required|boolean',
            'saturday'=>'required|boolean',
            'sunday'=>'required|boolean',
        ];
    }
}
