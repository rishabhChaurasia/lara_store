<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Will implement proper authorization later
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->route('category'); // Check if this is an update request (has category parameter)

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Max 2MB image
            'is_active' => ['boolean'],
            'parent_id' => ['nullable', 'exists:categories,id'], // For nested categories
        ];

        // We don't validate slug in form request since it's auto-generated in the controller
        // The uniqueness will be handled in the controller for both create and update

        return $rules;
    }
}
