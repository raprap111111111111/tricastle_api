<?php
namespace App\Http\Requests\v1\Portal;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPortalRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date'],
            'email' => ['required', 'email', 'unique:applicant_portal_accounts,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
