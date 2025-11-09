<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
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
            'message' => 'required|string|min:10|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
            'feedback_type' => 'nullable|string|in:general,bug,feature,navigation,other',
            'g-recaptcha-response' => 'required', // For reCAPTCHA v3
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Please provide your feedback.',
            'message.min' => 'Feedback must be at least 10 characters.',
            'message.max' => 'Feedback cannot exceed 1000 characters.',
            'rating.min' => 'Rating must be between 1 and 5.',
            'rating.max' => 'Rating must be between 1 and 5.',
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ];
    }
}
