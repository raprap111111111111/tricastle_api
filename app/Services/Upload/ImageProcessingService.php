<?php

// app/Services/Upload/ImageProcessingService.php

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageProcessingService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function process(UploadedFile $file): string
    {
        $image   = $this->manager->read($file->getPathname());
        $maxW    = config('upload.image.max_width', 2048);
        $maxH    = config('upload.image.max_height', 2048);
        $quality = config('upload.image.quality', 85);

        if ($image->width() > $maxW || $image->height() > $maxH) {
            $image->scaleDown(width: $maxW, height: $maxH);
        }

        $image->orient();

        return $image->toJpeg(quality: $quality)->toString();
    }

    public function crop(UploadedFile $file, int $width, int $height, int $x = 0, int $y = 0): string
    {
        $quality = config('upload.image.quality', 85);

        return $this->manager
            ->read($file->getPathname())
            ->crop($width, $height, $x, $y)
            ->toJpeg(quality: $quality)
            ->toString();
    }

    public function resize(UploadedFile $file, int $width, int $height): string
    {
        $quality = config('upload.image.quality', 85);

        return $this->manager
            ->read($file->getPathname())
            ->scale(width: $width, height: $height)
            ->toJpeg(quality: $quality)
            ->toString();
    }

    public function toWebp(UploadedFile $file, int $quality = 85): string
    {
        return $this->manager
            ->read($file->getPathname())
            ->orient()
            ->toWebp(quality: $quality)
            ->toString();
    }

    public function getDimensions(UploadedFile $file): array
    {
        $image = $this->manager->read($file->getPathname());

        return [
            'width'  => $image->width(),
            'height' => $image->height(),
        ];
    }
}
