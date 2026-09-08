<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Models\ApplicantDocument;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamApplicantDocumentAction
{
    /** @param  'inline'|'attachment'  $disposition */
    public function execute(
        ApplicantDocument $document,
        string $disposition = 'inline',
    ): StreamedResponse {
        [$disk, $path] = $this->resolve($document);

        if (!$disk || !$path) {
            abort(404, 'File not found on storage.');
        }

        $mime     = $document->mime_type ?? 'application/octet-stream';
        $filename = str_replace(['"', "\r", "\n"], '', $document->file_name ?? 'document');

        return response()->stream(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            if ($stream) {
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, [
            'Content-Type'           => $mime,
            'Content-Disposition'    => "{$disposition}; filename=\"{$filename}\"",
            'Cache-Control'          => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** @return array{0: Filesystem|null, 1: string|null} */
    private function resolve(ApplicantDocument $doc): array
    {
        $path = $doc->file_path
            ?? $doc->fileRepository?->file_path
            ?? null;

        if (empty($path)) {
            return [null, null];
        }

        $path = str_replace('\\', '/', (string) $path);

        $preferred = $doc->disk
            ?? $doc->fileRepository?->disk
            ?? $doc->fileRepository?->storage_driver
            ?? null;

        $candidates = array_values(array_unique(array_filter([
            $preferred,
            config('filesystems.default', 'public'),
            'r2',
            's3',
            'public',
            'local',
        ])));

        foreach ($candidates as $name) {
            try {
                $disk = Storage::disk($name);
                if ($disk->exists($path)) {
                    return [$disk, $path];
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return [null, null];
    }
}