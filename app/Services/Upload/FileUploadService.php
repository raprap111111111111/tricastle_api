<?php

// app/Services/Upload/FileUploadService.php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function __construct(
        private readonly ImageProcessingService $imageProcessor,
        private readonly ThumbnailService       $thumbnailService,
    ) {}

    public function upload(
        UploadedFile $file,
        string       $folder = 'uploads/documents',
        ?string      $disk = null,
        bool         $generateThumbnail = false,
        bool         $isEncrypted = false,
    ): array {
        $disk     = $disk ?? config('upload.disk', 'public');
        $fileName = $this->generateFileName($file);
        $path     = $folder . '/' . $fileName;

        if ($this->isImage($file)) {
            $processedFile = $this->imageProcessor->process($file);
            Storage::disk($disk)->put($path, $processedFile, 'public');
        } else {
            Storage::disk($disk)->putFileAs($folder, $file, $fileName, 'public');
        }

        $thumbnailPath = null;
        if ($generateThumbnail && $this->isImage($file)) {
            $thumbnailPath = $this->thumbnailService->generate(
                file:   $file,
                folder: config('upload.paths.thumbnails'),
                disk:   $disk,
            );
        }

        return [
            'original_name'  => $file->getClientOriginalName(),
            'file_name'      => $fileName,
            'file_path'      => $path,
            'thumbnail_path' => $thumbnailPath,
            'file_size'      => $file->getSize(),
            'mime_type'      => $file->getMimeType(),
            'extension'      => $file->getClientOriginalExtension(),
            'disk'           => $disk,
            'url'            => $this->getUrl($path, $disk),
            'is_encrypted'   => $isEncrypted,
        ];
    }

    public function uploadMany(
        array   $files,
        string  $folder = 'uploads/documents',
        ?string $disk = null,
        bool    $generateThumbnail = false,
    ): array {
        $uploaded = [];

        foreach ($files as $file) {
            $uploaded[] = $this->upload(
                file:              $file,
                folder:            $folder,
                disk:              $disk,
                generateThumbnail: $generateThumbnail,
            );
        }

        return $uploaded;
    }

    public function delete(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? config('upload.disk', 'public');

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    public function deleteWithThumbnail(string $path, ?string $thumbnailPath = null, ?string $disk = null): bool
    {
        $deleted = $this->delete($path, $disk);

        if ($thumbnailPath) {
            $this->delete($thumbnailPath, $disk);
        }

        return $deleted;
    }

    public function replace(
        UploadedFile $newFile,
        string       $oldPath,
        string       $folder = 'uploads/documents',
        ?string      $disk = null,
        bool         $generateThumbnail = false,
    ): array {
        $this->delete($oldPath, $disk);

        return $this->upload(
            file:              $newFile,
            folder:            $folder,
            disk:              $disk,
            generateThumbnail: $generateThumbnail,
        );
    }

    public function getUrl(string $path, ?string $disk = null): string
    {
        $disk = $disk ?? config('upload.disk', 'public');

        if ($disk === 's3') {
            return Storage::disk('s3')->url($path);
        }

        return Storage::disk($disk)->url($path);
    }

    public function getTemporaryUrl(string $path, int $minutes = 60, string $disk = 's3'): string
    {
        return Storage::disk($disk)->temporaryUrl(
            $path,
            now()->addMinutes($minutes),
        );
    }

    public function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    public function exists(string $path, ?string $disk = null): bool
    {
        $disk = $disk ?? config('upload.disk', 'public');
        return Storage::disk($disk)->exists($path);
    }

    private function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $uuid      = Str::uuid();
        $timestamp = now()->format('YmdHis');

        return "{$timestamp}_{$uuid}.{$extension}";
    }
}
