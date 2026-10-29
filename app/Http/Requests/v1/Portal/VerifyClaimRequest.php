<?php
namespace App\Http\Requests\v1\Portal;

use Illuminate\Foundation\Http\FormRequest;

class VerifyClaimRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date'],
        ];
    }
}
