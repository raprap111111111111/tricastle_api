<?php
// app/Http/Requests/v1/User/DeleteUserRequest.php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        // Prevent self-deletion
        if ($this->user()->id === $target->id) {
            return false;
        }

        return $this->user()->can('delete', $target);
    }

    public function rules(): array
    {
        return [];
    }
}