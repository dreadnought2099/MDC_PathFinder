<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from message
        if ($this->has('message')) {
            $this->merge([
                'message' => trim($this->input('message'))
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => [
                'required',
                'string',
                'min:10',
                'max:1000',
                function ($attribute, $value, $fail) {
                    // Check if message is not just repeated characters
                    if (preg_match('/^(.)\1+$/', $value)) {
                        $fail('Please provide meaningful feedback.');
                    }
                    // Check if message contains actual words
                    if (!preg_match('/[a-zA-Z]{2,}/', $value)) {
                        $fail('Please provide feedback in readable text.');
                    }
                }
            ],
            'rating' => [
                'nullable',
                'integer',
                'min:1',
                'max:5'
            ],
            'feedback_type' => [
                'required',
                'string',
                Rule::in(['general', 'bug', 'feature', 'navigation', 'other'])
            ],
            'page_url' => [
                'nullable',
                'string',
                'url',
                'max:500'
            ],
            'g-recaptcha-response' => [
                'required',
                'string'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Please provide your feedback.',
            'message.min' => 'Feedback must be at least 10 characters.',
            'message.max' => 'Feedback cannot exceed 1000 characters.',
            'rating.integer' => 'Rating must be a valid number.',
            'rating.min' => 'Rating must be between 1 and 5 stars.',
            'rating.max' => 'Rating must be between 1 and 5 stars.',
            'feedback_type.required' => 'Please select a feedback type.',
            'feedback_type.in' => 'Invalid feedback type selected.',
            'page_url.url' => 'Invalid page URL format.',
            'g-recaptcha-response.required' => 'Please complete the security verification.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'message' => 'feedback message',
            'rating' => 'star rating',
            'feedback_type' => 'feedback type',
            'page_url' => 'page URL',
            'g-recaptcha-response' => 'reCAPTCHA verification',
        ];
    }
}
