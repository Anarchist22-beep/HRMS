<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;



class UserUpdateRequest extends ApiRequest
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
            'name'        => 'required|string|max:255',
            'email'       => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('id'))
            ],
            'password'    => 'nullable|min:8', //  IMPORTANT
            'role'        => 'required',
            'designation' => 'required|string',
            'salary'      => 'required|numeric',
            'depart_id'   => 'required|exists:departments,id',
            'project_id'  => 'nullable|exists:projects,id'
        ];
    }
}
