<?php

// app/Domain/OcrTemplate/Mappers/OcrTemplateMapper.php

namespace App\Domain\OcrTemplate\Mappers;

use App\Domain\OcrTemplate\DTOs\ApproveOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\CompleteOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\CreateOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\RejectOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\UpdateOcrTemplateDTO;
use App\Http\Requests\v1\OcrTemplate\ApproveOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\CompleteOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\StoreOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\UpdateOcrTemplateRequest;
use App\Http\Requests\v1\OcrTemplate\RejectOcrTemplateRequest;

class OcrTemplateMapper
{
    public static function fromStoreRequest(StoreOcrTemplateRequest $request): CreateOcrTemplateDTO
    {
        return new CreateOcrTemplateDTO(
            documentTypeId:        $request->validated('document_type_id'),
            name:                  $request->validated('name'),
            code:                  $request->validated('code'),
            version:               $request->validated('version', '1.0.0'),
            description:           $request->validated('description'),
            thumbnailPath:         $request->validated('thumbnail_path'),

            detectionKeywords:     $request->validated('detection_keywords'),
            detectionPatterns:     $request->validated('detection_patterns'),
            detectionFeatures:     $request->validated('detection_features'),
            sampleImagePath:       $request->validated('sample_image_path'),
            detectionThreshold:    $request->validated('detection_threshold', 75.00),

            expectedWidth:         $request->validated('expected_width'),
            expectedHeight:        $request->validated('expected_height'),
            aspectRatio:           $request->validated('aspect_ratio'),
            orientation:           $request->validated('orientation', 'portrait'),
            paperSize:             $request->validated('paper_size'),
            expectedPages:         $request->validated('expected_pages', 1),
            colorMode:             $request->validated('color_mode'),

            fieldDefinitions:      $request->validated('field_definitions', []),
            fieldPositions:        $request->validated('field_positions'),
            fieldRelationships:    $request->validated('field_relationships'),
            validationRules:       $request->validated('validation_rules', []),
            requiredFields:        $request->validated('required_fields'),
            optionalFields:        $request->validated('optional_fields'),

            preferredProvider:     $request->validated('preferred_provider'),
            providerSettings:      $request->validated('provider_settings'),
            fallbackProviders:     $request->validated('fallback_providers'),
            confidenceThreshold:   $request->validated('confidence_threshold', 70),

            requiresPreprocessing: $request->validated('requires_preprocessing', false),
            preprocessingSteps:    $request->validated('preprocessing_steps'),
            autoRotate:            $request->validated('auto_rotate', true),
            autoEnhance:           $request->validated('auto_enhance', false),
            autoDeskew:            $request->validated('auto_deskew', true),
            removeBackground:      $request->validated('remove_background', false),
            binarize:              $request->validated('binarize', false),

            primaryLanguage:       $request->validated('primary_language', 'en'),
            supportedLanguages:    $request->validated('supported_languages'),
            countryCode:           $request->validated('country_code', 'PH'),
            region:                $request->validated('region'),

            isActive:              $request->validated('is_active', true),
            isDefault:             $request->validated('is_default', false),
            isVerified:            $request->validated('is_verified', false),
            isPublic:              $request->validated('is_public', true),
            isBeta:                $request->validated('is_beta', false),
            priority:              $request->validated('priority', 5),

            allowedRoles:          $request->validated('allowed_roles'),
            restrictedUsers:       $request->validated('restricted_users'),

            notes:                 $request->validated('notes'),
            tags:                  $request->validated('tags'),
            metadata:              $request->validated('metadata'),
            changelog:             $request->validated('changelog'),
            createdBy:             $request->user()->id,
        );
    }

    public static function fromUpdateRequest(UpdateOcrTemplateRequest $request): UpdateOcrTemplateDTO
    {
        return new UpdateOcrTemplateDTO(
            documentTypeId:        $request->validated('document_type_id'),
            name:                  $request->validated('name'),
            code:                  $request->validated('code'),
            version:               $request->validated('version'),
            description:           $request->validated('description'),
            thumbnailPath:         $request->validated('thumbnail_path'),

            detectionKeywords:     $request->validated('detection_keywords'),
            detectionPatterns:     $request->validated('detection_patterns'),
            detectionFeatures:     $request->validated('detection_features'),
            sampleImagePath:       $request->validated('sample_image_path'),
            detectionThreshold:    $request->validated('detection_threshold'),

            expectedWidth:         $request->validated('expected_width'),
            expectedHeight:        $request->validated('expected_height'),
            aspectRatio:           $request->validated('aspect_ratio'),
            orientation:           $request->validated('orientation'),
            paperSize:             $request->validated('paper_size'),
            expectedPages:         $request->validated('expected_pages'),
            colorMode:             $request->validated('color_mode'),

            fieldDefinitions:      $request->validated('field_definitions'),
            fieldPositions:        $request->validated('field_positions'),
            fieldRelationships:    $request->validated('field_relationships'),
            validationRules:       $request->validated('validation_rules'),
            requiredFields:        $request->validated('required_fields'),
            optionalFields:        $request->validated('optional_fields'),

            preferredProvider:     $request->validated('preferred_provider'),
            providerSettings:      $request->validated('provider_settings'),
            fallbackProviders:     $request->validated('fallback_providers'),
            confidenceThreshold:   $request->validated('confidence_threshold'),

            requiresPreprocessing: $request->validated('requires_preprocessing'),
            preprocessingSteps:    $request->validated('preprocessing_steps'),
            autoRotate:            $request->validated('auto_rotate'),
            autoEnhance:           $request->validated('auto_enhance'),
            autoDeskew:            $request->validated('auto_deskew'),
            removeBackground:      $request->validated('remove_background'),
            binarize:              $request->validated('binarize'),

            primaryLanguage:       $request->validated('primary_language'),
            supportedLanguages:    $request->validated('supported_languages'),
            countryCode:           $request->validated('country_code'),
            region:                $request->validated('region'),

            isActive:              $request->validated('is_active'),
            isDefault:             $request->validated('is_default'),
            isPublic:              $request->validated('is_public'),
            isBeta:                $request->validated('is_beta'),
            priority:              $request->validated('priority'),

            allowedRoles:          $request->validated('allowed_roles'),
            restrictedUsers:       $request->validated('restricted_users'),

            notes:                 $request->validated('notes'),
            tags:                  $request->validated('tags'),
            metadata:              $request->validated('metadata'),
            changelog:             $request->validated('changelog'),
            updatedBy:             $request->user()->id,
        );
    }

    public static function fromApproveRequest(ApproveOcrTemplateRequest $request): ApproveOcrTemplateDTO
    {
        return new ApproveOcrTemplateDTO(
            approvedBy: $request->user()->id,
            notes:      $request->validated('notes'),
        );
    }

    public static function fromRejectRequest(RejectOcrTemplateRequest $request): RejectOcrTemplateDTO
    {
        return new RejectOcrTemplateDTO(
            rejectedBy: $request->user()->id,
            reason:     $request->validated('reason'),
            notes:      $request->validated('notes'),
        );
    }

    public static function fromCompleteRequest(CompleteOcrTemplateRequest $request): CompleteOcrTemplateDTO
    {
        return new CompleteOcrTemplateDTO(
            completedBy: $request->user()->id,
            notes:       $request->validated('notes'),
        );
    }
}