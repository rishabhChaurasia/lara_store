<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
        $isUpdate = $this->route('product'); // Check if this is an update request (has product parameter)

        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'], // Stored as unsignedInteger (cents)
            'sale_price' => ['nullable', 'integer', 'min:0', 'lt:price'], // Must be less than regular price
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Max 2MB image
        ];

        // For updates, we need SKU validation to ensure uniqueness with exception of current record
        if ($isUpdate) {
            $rules['sku'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($this->route('product')->id)
            ];
        } else {
            // For creation, require unique SKU
            $rules['sku'] = ['required', 'string', 'max:255', 'unique:products,sku'];
        }

        // We don't validate slug in form request since it's auto-generated in the controller
        // The uniqueness will be handled in the controller for both create and update

        return $rules;
    }
}
