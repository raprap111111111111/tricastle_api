<?php

namespace App\Domain\User\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function uploadAvatar(UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }

    public function deleteAvatar(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
