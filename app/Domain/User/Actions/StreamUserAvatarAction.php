<?php

namespace App\Domain\User\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamUserAvatarAction
{
    public function execute(User $user): StreamedResponse
    {
        $path = $user->avatar;

        if (empty($path)) {
            abort(404, 'Avatar not found.');
        }

        $diskName = config('filesystems.default', 'public');
        $disk     = Storage::disk($diskName);

        // Fallback check on 'public' if not in R2
        if (!$disk->exists($path)) {
            $disk = Storage::disk('public');
        }

        if (!$disk->exists($path)) {
            abort(404, 'Avatar file missing from storage.');
        }

        $mime     = $disk->mimeType($path) ?? 'image/jpeg';
        $filename = basename($path);

        return response()->stream(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            if ($stream) {
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}