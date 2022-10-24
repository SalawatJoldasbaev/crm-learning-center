<?php

namespace App\Http\Requests\Employee;

use App\Src\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends FormRequest
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
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required',
            'phone' => 'required|unique:employees,phone,' . $this->employee_id,
            'file_id' => 'nullable|exists:files,uuid',
            'roles' => 'required|array',
            'gender' => 'required|required_with:male,female',
            'salary' => 'nullable|numeric',
            'password' => 'nullable',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error('error', $validator->errors()->toArray(), code: 422));
    }
}
