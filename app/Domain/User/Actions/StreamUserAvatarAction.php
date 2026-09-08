<?php

namespace App\Domain\User\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StreamUserAvatarAction
{
    public function execute(User $user): StreamedResponse
    {
        $path = $user->getRawOriginal('avatar');

        // No avatar or external OAuth URL
        if (empty($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            throw new NotFoundHttpException('Avatar not found.');
        }

        $path = str_replace('\\', '/', $path);
        $diskName = config('filesystems.default', 'public');

        // Try default disk first, then common fallbacks
        $candidates = array_unique(array_filter([
            $diskName,
            'r2',
            's3',
            'public',
            'local',
        ]));

        $disk = null;
        foreach ($candidates as $name) {
            try {
                if (Storage::disk($name)->exists($path)) {
                    $disk = Storage::disk($name);
                    break;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        if (! $disk) {
            throw new NotFoundHttpException('Avatar file not found in storage.');
        }

        // ─── MIME type (Intelephense-safe) ───────────────────────────────
        $mime = 'image/jpeg'; // safe default
        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $detected = $disk->mimeType($path);
            if (is_string($detected) && $detected !== '') {
                $mime = $detected;
            }
        } catch (\Throwable) {
            // ignore – keep default
        }

        $stream = $disk->readStream($path);

        if ($stream === false) {
            throw new NotFoundHttpException('Unable to read avatar.');
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'        => $mime,
            'Cache-Control'       => 'public, max-age=86400',
            'Content-Disposition' => 'inline; filename="avatar"',
        ]);
    }
}