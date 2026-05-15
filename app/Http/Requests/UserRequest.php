<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UserRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name'=>'required|string|max:255',
            'email'=>'required','email',Rule::unique('users', 'email')->ignore($this->route('id')),
            'password'=>'required|min:8',
            'role'=>'required',
            'designation'=>'required|string',
            'salary'=>'required|numeric',
            'depart_id'=>'required|exists:departments,id',
            'project_id' =>'nullable|exists:projects,id'

        ];
    }
}
