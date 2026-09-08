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

    /**
     * Upload avatar to default disk (R2 when FILESYSTEM_DISK=r2).
     */
    public function uploadAvatar(UploadedFile $file): string
    {
        $disk = config('filesystems.default', 'public');

        return $file->store('avatars', $disk);
    }

    /**
     * Delete avatar from storage (skip external URLs).
     */
    public function deleteAvatar(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $path = str_replace('\\', '/', $path);

        // OAuth / full URL avatars are not stored on our disk
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        $candidates = array_values(array_unique(array_filter([
            config('filesystems.default', 'public'),
            'r2',
            's3',
            'public',
            'local',
        ])));

        foreach ($candidates as $diskName) {
            try {
                $disk = Storage::disk($diskName);
                if ($disk->exists($path)) {
                    $disk->delete($path);
                    return;
                }
            } catch (\Throwable) {
                continue;
            }
        }
    }
}