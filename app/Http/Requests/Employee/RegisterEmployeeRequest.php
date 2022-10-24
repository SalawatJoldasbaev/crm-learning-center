<?php

namespace App\Http\Requests\Employee;

use App\Src\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => 'required|unique:teachers,phone',
            'file_id' => 'nullable|exists:files,uuid',
            'password' => 'required',
            'roles' => 'required|array',
            'gender' => 'required_with:male,female',
            'salary' => 'nullable|numeric',
            'salary_percentage' => 'nullable',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error('error', $validator->errors()->toArray(), code: 422));
    }
}
