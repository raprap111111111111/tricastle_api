<?php

// config/upload.php

return [
    'disk'     => env('FILESYSTEM_DISK', 'public'),
    'url'      => env('APP_URL') . '/storage',
    'max_size' => env('UPLOAD_MAX_FILE_SIZE', 10240),

    'allowed_mimes' => [
        'image'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
        'archive'  => ['zip', 'rar', '7z'],
    ],

    'image' => [
        'quality'    => env('UPLOAD_IMAGE_QUALITY', 85),
        'max_width'  => 2048,
        'max_height' => 2048,
    ],

    'thumbnail' => [
        'width'   => env('UPLOAD_THUMBNAIL_WIDTH', 300),
        'height'  => env('UPLOAD_THUMBNAIL_HEIGHT', 300),
        'quality' => 80,
        'suffix'  => '_thumb',
    ],

    'paths' => [
        'documents'  => 'uploads/documents',
        'images'     => 'uploads/images',
        'thumbnails' => 'uploads/thumbnails',
        'temp'       => 'uploads/temp',
        'avatars'    => 'uploads/avatars',
    ],
];
