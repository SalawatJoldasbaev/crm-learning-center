<?php

namespace App\Http\Requests\Course;

use App\Src\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CourseCreateRequest extends FormRequest
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
            'file_id' => 'nullable|exists:files,id',
            'name' => 'required',
            'price' => 'required',
            'lesson_duration' => 'required',
            'month' => 'required',
            'description' => 'nullable',
            'lessons_per_module' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error('error', $validator->errors()->toArray(), code: 422));
    }
}
