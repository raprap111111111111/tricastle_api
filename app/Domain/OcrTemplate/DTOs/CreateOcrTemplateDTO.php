<?php

// app/Domain/OcrTemplate/DTOs/CreateOcrTemplateDTO.php

namespace App\Domain\OcrTemplate\DTOs;

final readonly class CreateOcrTemplateDTO
{
    public function __construct(
        public int     $documentTypeId,
        public string  $name,
        public string  $code,
        public string  $version = '1.0.0',
        public ?string $description = null,
        public ?string $thumbnailPath = null,

        // Detection
        public ?array  $detectionKeywords = null,
        public ?array  $detectionPatterns = null,
        public ?array  $detectionFeatures = null,
        public ?string $sampleImagePath = null,
        public float   $detectionThreshold = 75.00,

        // Specifications
        public ?int    $expectedWidth = null,
        public ?int    $expectedHeight = null,
        public ?float  $aspectRatio = null,
        public string  $orientation = 'portrait',
        public ?string $paperSize = null,
        public int     $expectedPages = 1,
        public ?string $colorMode = null,

        // Fields
        public array   $fieldDefinitions = [],
        public ?array  $fieldPositions = null,
        public ?array  $fieldRelationships = null,
        public array   $validationRules = [],
        public ?array  $requiredFields = null,
        public ?array  $optionalFields = null,

        // OCR Provider
        public ?string $preferredProvider = null,
        public ?array  $providerSettings = null,
        public ?array  $fallbackProviders = null,
        public int     $confidenceThreshold = 70,

        // Image Processing
        public bool    $requiresPreprocessing = false,
        public ?array  $preprocessingSteps = null,
        public bool    $autoRotate = true,
        public bool    $autoEnhance = false,
        public bool    $autoDeskew = true,
        public bool    $removeBackground = false,
        public bool    $binarize = false,

        // Language & Region
        public string  $primaryLanguage = 'en',
        public ?array  $supportedLanguages = null,
        public string  $countryCode = 'PH',
        public ?string $region = null,

        // Status & Config
        public bool    $isActive = true,
        public bool    $isDefault = false,
        public bool    $isVerified = false,
        public bool    $isPublic = true,
        public bool    $isBeta = false,
        public int     $priority = 5,

        // Access Control
        public ?array  $allowedRoles = null,
        public ?array  $restrictedUsers = null,

        // Meta
        public ?string $notes = null,
        public ?array  $tags = null,
        public ?array  $metadata = null,
        public ?string $changelog = null,
        public ?int    $createdBy = null,
    ) {}
}