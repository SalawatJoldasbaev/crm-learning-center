<?php

namespace App\Http\Requests\Group;

use App\Src\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GroupCreateRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            'room_id' => 'required|exists:rooms,id',
            'time_id' => 'required|exists:time_courses,id',
            'teacher_ids' => 'required',
            'name' => 'required',
            'days' => 'required|array',
            'group_start_date' => 'required',
            'group_end_date' => 'nullable',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error('error', $validator->errors()->toArray(), code: 422));
    }
}
