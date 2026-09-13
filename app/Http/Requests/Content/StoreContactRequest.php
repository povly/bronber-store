<?php

declare(strict_types=1);

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation of the contacts form field set. The fields are fixed in
 * code; lead storage is form-agnostic and lives in the external CRM.
 */
class StoreContactRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Field labels for validation messages (per-locale).
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('store.contacts_form_name'),
            'phone' => __('store.contacts_form_phone'),
            'email' => __('store.contacts_form_email'),
            'message' => __('store.contacts_form_subject'),
        ];
    }
}
