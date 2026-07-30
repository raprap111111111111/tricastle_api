<?php

// app/Domain/OcrTemplate/DTOs/UpdateOcrTemplateDTO.php

namespace App\Domain\OcrTemplate\DTOs;

final readonly class UpdateOcrTemplateDTO
{
    public function __construct(
        public ?int    $documentTypeId = null,
        public ?string $name = null,
        public ?string $code = null,
        public ?string $version = null,
        public ?string $description = null,
        public ?string $thumbnailPath = null,

        // Detection
        public ?array  $detectionKeywords = null,
        public ?array  $detectionPatterns = null,
        public ?array  $detectionFeatures = null,
        public ?string $sampleImagePath = null,
        public ?float  $detectionThreshold = null,

        // Specifications
        public ?int    $expectedWidth = null,
        public ?int    $expectedHeight = null,
        public ?float  $aspectRatio = null,
        public ?string $orientation = null,
        public ?string $paperSize = null,
        public ?int    $expectedPages = null,
        public ?string $colorMode = null,

        // Fields
        public ?array  $fieldDefinitions = null,
        public ?array  $fieldPositions = null,
        public ?array  $fieldRelationships = null,
        public ?array  $validationRules = null,
        public ?array  $requiredFields = null,
        public ?array  $optionalFields = null,

        // OCR Provider
        public ?string $preferredProvider = null,
        public ?array  $providerSettings = null,
        public ?array  $fallbackProviders = null,
        public ?int    $confidenceThreshold = null,

        // Image Processing
        public ?bool   $requiresPreprocessing = null,
        public ?array  $preprocessingSteps = null,
        public ?bool   $autoRotate = null,
        public ?bool   $autoEnhance = null,
        public ?bool   $autoDeskew = null,
        public ?bool   $removeBackground = null,
        public ?bool   $binarize = null,

        // Language & Region
        public ?string $primaryLanguage = null,
        public ?array  $supportedLanguages = null,
        public ?string $countryCode = null,
        public ?string $region = null,

        // Status & Config
        public ?bool   $isActive = null,
        public ?bool   $isDefault = null,
        public ?bool   $isPublic = null,
        public ?bool   $isBeta = null,
        public ?int    $priority = null,

        // Access Control
        public ?array  $allowedRoles = null,
        public ?array  $restrictedUsers = null,

        // Meta
        public ?string $notes = null,
        public ?array  $tags = null,
        public ?array  $metadata = null,
        public ?string $changelog = null,
        public ?int    $updatedBy = null,
    ) {}
}