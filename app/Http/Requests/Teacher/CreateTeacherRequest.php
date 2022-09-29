<?php

namespace App\Http\Requests\Teacher;

use App\Src\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateTeacherRequest extends FormRequest
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
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required',
            'phone' => 'required|unique:employees,phone',
            'file_id' => 'nullable|exists:files,uuid',
            'password' => 'required',
            'gender' => 'required_with:male,female',
            'salary_percentage' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error('error', $validator->errors()->toArray(), code: 422));
    }
}
