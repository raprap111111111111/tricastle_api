<?php

// app/Services/Upload/ThumbnailService.php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ThumbnailService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function generate(
        UploadedFile $file,
        string       $folder = 'uploads/thumbnails',
        ?string      $disk = null,
        ?int         $width = null,
        ?int         $height = null,
    ): string {
        $disk      = $disk ?? config('upload.disk', 'public');
        $width     = $width  ?? config('upload.thumbnail.width', 300);
        $height    = $height ?? config('upload.thumbnail.height', 300);
        $quality   = config('upload.thumbnail.quality', 80);
        $suffix    = config('upload.thumbnail.suffix', '_thumb');
        $extension = $file->getClientOriginalExtension();
        $fileName  = Str::uuid() . $suffix . '.' . $extension;
        $path      = $folder . '/' . $fileName;

        $thumbnail = $this->manager
            ->read($file->getPathname())
            ->orient()
            ->cover(width: $width, height: $height)
            ->toJpeg(quality: $quality)
            ->toString();

        Storage::disk($disk)->put($path, $thumbnail, 'public');

        return $path;
    }

    public function generateSizes(
        UploadedFile $file,
        string       $folder = 'uploads/thumbnails',
        ?string      $disk = null,
    ): array {
        $sizes = [
            'small'  => ['width' => 100, 'height' => 100],
            'medium' => ['width' => 300, 'height' => 300],
            'large'  => ['width' => 600, 'height' => 600],
        ];

        $thumbnails = [];

        foreach ($sizes as $size => $dimensions) {
            $thumbnails[$size] = $this->generate(
                file:   $file,
                folder: $folder . '/' . $size,
                disk:   $disk,
                width:  $dimensions['width'],
                height: $dimensions['height'],
            );
        }

        return $thumbnails;
    }
}
