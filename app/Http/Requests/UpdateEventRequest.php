<?php

namespace App\Http\Requests;

use App\Rules\NoOverlappingEvents;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after:from_date'],
            'video' => ['nullable', 'string'],
            'banner_image' => ['nullable', 'string'],
            'other_information' => ['nullable', 'string'],
            'artists' => ['nullable', 'array'],
            'artists.*' => ['exists:artists,id'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('from_date') && $this->has('to_date')) {
                $eventId = $this->route('event') ?? $this->route('id');
                $validator->addRules([
                    'from_date' => [new NoOverlappingEvents($this->from_date, $this->to_date, $eventId)],
                ]);
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'to_date.after' => 'The event end date must be after the start date.',
            'artists.*.exists' => 'One or more selected artists do not exist.',
        ];
    }
}
