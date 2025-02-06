<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Default rules for POST (create)
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];

        // For PUT/PATCH (update), make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['title'] = 'sometimes|string|max:255';
            $rules['content'] = 'sometimes|string';
        }

        return $rules;
    }
}
