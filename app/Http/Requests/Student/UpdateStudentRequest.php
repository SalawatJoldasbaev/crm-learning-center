<?php

namespace App\Http\Requests\Student;

use App\Src\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStudentRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|unique:students,phone,' . $this->student_id,
            'password' => 'nullable',
            'address' => 'required',
            'birthday' => 'required',
            'gender' => 'required',
            'addition_phone' => 'nullable|array'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error('error', $validator->errors()->toArray(), code: 422));
    }
}
