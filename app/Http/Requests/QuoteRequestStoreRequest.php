<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteRequestStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $extensions = implode(',', config('quote.attachment.extensions'));

        return [
            'name' => ['required', 'string', 'max:191'],
            'company' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['required', 'string', 'max:50'],
            'service' => ['required', 'string', 'exists:services,slug'],
            'location' => ['required', Rule::in(config('quote.cities'))],
            'location_other' => ['nullable', 'required_if:location,other', 'string', 'max:191'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', "mimes:{$extensions}", 'max:'.config('quote.attachment.max_kb')],
            'website' => ['nullable', 'size:0'], // honeypot
            'captcha_answer' => ['required', 'integer'],
            'form_ts' => ['required', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('service')) {
            $this->merge(['service_slug' => $this->input('service')]);
        }
    }
}
