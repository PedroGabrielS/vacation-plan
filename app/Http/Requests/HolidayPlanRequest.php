<?php

namespace App\Http\Requests;

use App\Http\Requests\AppRequest;
use Illuminate\Validation\Rule;

class HolidayPlanRequest extends AppRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'date' => 'required|date_format:Y-m-d',
            'location' => 'required|string',
            'participants' => [
                'nullable',
                'array',
            ],
        ];
    }

     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'description.string' => 'The description field must be a string.',
            'date.required' => 'The date field is required.',
            'date.date_format' => 'The date must be in the format Y-m-d.',
            'location.required' => 'The location field is required.',
            'location.string' => 'The location field must be a string.',
            'participants.array' => 'The participants field must be an array.',
        ];
    }
}
