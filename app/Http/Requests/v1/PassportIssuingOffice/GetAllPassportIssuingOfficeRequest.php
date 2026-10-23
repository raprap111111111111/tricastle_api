<?php

namespace App\Http\Requests\v1\PassportIssuingOffice;

use App\Models\PassportIssuingOffice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAllPassportIssuingOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', PassportIssuingOffice::class);
    }

    public function rules(): array
    {
        return [
            'search'      => ['nullable', 'string', 'max:100'],
            'region'      => ['nullable', 'string', 'max:50'],
            'is_active'   => ['nullable', 'boolean'],
            'active_only' => ['nullable', 'boolean'],
            'order_by'    => ['nullable', Rule::in(['id', 'name', 'region', 'city', 'province', 'sort_order', 'created_at'])],
            'order_dir'   => ['nullable', Rule::in(['asc', 'desc'])],
            'paginate'    => ['nullable', 'boolean'],
            'limit'       => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}