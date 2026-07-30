<?php

// app/Domain/OcrTemplate/Repositories/OcrTemplateRepository.php

namespace App\Domain\OcrTemplate\Repositories;

use App\Domain\OcrTemplate\DTOs\ApproveOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\CompleteOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\CreateOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\RejectOcrTemplateDTO;
use App\Domain\OcrTemplate\DTOs\UpdateOcrTemplateDTO;
use App\Models\OcrTemplate;
use App\Support\Query\BaseRepository;

class OcrTemplateRepository extends BaseRepository
{
    protected string $model = OcrTemplate::class;

    protected array $relations = [
        'documentType',
        'createdByUser',
        'updatedByUser',
        'approvedByUser',
    ];

    protected array $searchable = [
        'name',
        'code',
        'description',
    ];

    protected array $filterable = [
        'document_type_id',
        'preferred_provider',
        'primary_language',
        'country_code',
        'orientation',
        'is_active',
        'is_default',
        'is_verified',
        'is_public',
        'is_beta',
        'created_by',
    ];

    protected array $sortable = [
        'id',
        'name',
        'code',
        'version',
        'priority',
        'times_used',
        'success_rate',
        'avg_confidence',
        'last_used_at',
        'created_at',
        'updated_at',
    ];

    protected string $defaultOrderBy        = 'created_at';
    protected string $defaultOrderDirection = 'desc';

    public function createFromDTO(CreateOcrTemplateDTO $dto): OcrTemplate
    {
        return OcrTemplate::create([
            'document_type_id'        => $dto->documentTypeId,
            'name'                    => $dto->name,
            'code'                    => $dto->code,
            'version'                 => $dto->version,
            'description'             => $dto->description,
            'thumbnail_path'          => $dto->thumbnailPath,

            'detection_keywords'      => $dto->detectionKeywords,
            'detection_patterns'      => $dto->detectionPatterns,
            'detection_features'      => $dto->detectionFeatures,
            'sample_image_path'       => $dto->sampleImagePath,
            'detection_threshold'     => $dto->detectionThreshold,

            'expected_width'          => $dto->expectedWidth,
            'expected_height'         => $dto->expectedHeight,
            'aspect_ratio'            => $dto->aspectRatio,
            'orientation'             => $dto->orientation,
            'paper_size'              => $dto->paperSize,
            'expected_pages'          => $dto->expectedPages,
            'color_mode'              => $dto->colorMode,

            'field_definitions'       => $dto->fieldDefinitions,
            'field_positions'         => $dto->fieldPositions,
            'field_relationships'     => $dto->fieldRelationships,
            'validation_rules'        => $dto->validationRules,
            'required_fields'         => $dto->requiredFields,
            'optional_fields'         => $dto->optionalFields,

            'preferred_provider'      => $dto->preferredProvider,
            'provider_settings'       => $dto->providerSettings,
            'fallback_providers'      => $dto->fallbackProviders,
            'confidence_threshold'    => $dto->confidenceThreshold,

            'requires_preprocessing'  => $dto->requiresPreprocessing,
            'preprocessing_steps'     => $dto->preprocessingSteps,
            'auto_rotate'             => $dto->autoRotate,
            'auto_enhance'            => $dto->autoEnhance,
            'auto_deskew'             => $dto->autoDeskew,
            'remove_background'       => $dto->removeBackground,
            'binarize'                => $dto->binarize,

            'primary_language'        => $dto->primaryLanguage,
            'supported_languages'     => $dto->supportedLanguages,
            'country_code'            => $dto->countryCode,
            'region'                  => $dto->region,

            'is_active'               => $dto->isActive,
            'is_default'              => $dto->isDefault,
            'is_verified'             => $dto->isVerified,
            'is_public'               => $dto->isPublic,
            'is_beta'                 => $dto->isBeta,
            'priority'                => $dto->priority,

            'allowed_roles'           => $dto->allowedRoles,
            'restricted_users'        => $dto->restrictedUsers,

            'notes'                   => $dto->notes,
            'tags'                    => $dto->tags,
            'metadata'                => $dto->metadata,
            'changelog'               => $dto->changelog,
            'created_by'              => $dto->createdBy,
        ]);
    }

    public function updateFromDTO(OcrTemplate $ocrTemplate, UpdateOcrTemplateDTO $dto): OcrTemplate
    {
        $ocrTemplate->update(array_filter([
            'document_type_id'        => $dto->documentTypeId,
            'name'                    => $dto->name,
            'code'                    => $dto->code,
            'version'                 => $dto->version,
            'description'             => $dto->description,
            'thumbnail_path'          => $dto->thumbnailPath,

            'detection_keywords'      => $dto->detectionKeywords,
            'detection_patterns'      => $dto->detectionPatterns,
            'detection_features'      => $dto->detectionFeatures,
            'sample_image_path'       => $dto->sampleImagePath,
            'detection_threshold'     => $dto->detectionThreshold,

            'expected_width'          => $dto->expectedWidth,
            'expected_height'         => $dto->expectedHeight,
            'aspect_ratio'            => $dto->aspectRatio,
            'orientation'             => $dto->orientation,
            'paper_size'              => $dto->paperSize,
            'expected_pages'          => $dto->expectedPages,
            'color_mode'              => $dto->colorMode,

            'field_definitions'       => $dto->fieldDefinitions,
            'field_positions'         => $dto->fieldPositions,
            'field_relationships'     => $dto->fieldRelationships,
            'validation_rules'        => $dto->validationRules,
            'required_fields'         => $dto->requiredFields,
            'optional_fields'         => $dto->optionalFields,

            'preferred_provider'      => $dto->preferredProvider,
            'provider_settings'       => $dto->providerSettings,
            'fallback_providers'      => $dto->fallbackProviders,
            'confidence_threshold'    => $dto->confidenceThreshold,

            'requires_preprocessing'  => $dto->requiresPreprocessing,
            'preprocessing_steps'     => $dto->preprocessingSteps,
            'auto_rotate'             => $dto->autoRotate,
            'auto_enhance'            => $dto->autoEnhance,
            'auto_deskew'             => $dto->autoDeskew,
            'remove_background'       => $dto->removeBackground,
            'binarize'                => $dto->binarize,

            'primary_language'        => $dto->primaryLanguage,
            'supported_languages'     => $dto->supportedLanguages,
            'country_code'            => $dto->countryCode,
            'region'                  => $dto->region,

            'is_active'               => $dto->isActive,
            'is_default'              => $dto->isDefault,
            'is_public'               => $dto->isPublic,
            'is_beta'                 => $dto->isBeta,
            'priority'                => $dto->priority,

            'allowed_roles'           => $dto->allowedRoles,
            'restricted_users'        => $dto->restrictedUsers,

            'notes'                   => $dto->notes,
            'tags'                    => $dto->tags,
            'metadata'                => $dto->metadata,
            'changelog'               => $dto->changelog,
            'updated_by'              => $dto->updatedBy,
        ], fn($value) => $value !== null));

        return $ocrTemplate->refresh();
    }

    public function approve(OcrTemplate $ocrTemplate, ApproveOcrTemplateDTO $dto): OcrTemplate
    {
        $ocrTemplate->update([
            'is_verified'   => true,
            'approved_by'   => $dto->approvedBy,
            'approved_at'   => now(),
            'notes'         => $dto->notes ?? $ocrTemplate->notes,
        ]);

        return $ocrTemplate->refresh();
    }

    public function reject(OcrTemplate $ocrTemplate, RejectOcrTemplateDTO $dto): OcrTemplate
    {
        $ocrTemplate->update([
            'is_verified' => false,
            'is_active'   => false,
            'notes'       => $dto->notes ?? $ocrTemplate->notes,
            'metadata'    => array_merge($ocrTemplate->metadata ?? [], [
                'rejected_by'     => $dto->rejectedBy,
                'rejection_reason' => $dto->reason,
                'rejected_at'     => now()->toISOString(),
            ]),
        ]);

        return $ocrTemplate->refresh();
    }

    public function complete(OcrTemplate $ocrTemplate, CompleteOcrTemplateDTO $dto): OcrTemplate
    {
        $ocrTemplate->update([
            'is_beta'  => false,
            'is_active' => true,
            'notes'    => $dto->notes ?? $ocrTemplate->notes,
            'metadata' => array_merge($ocrTemplate->metadata ?? [], [
                'completed_by' => $dto->completedBy,
                'completed_at' => now()->toISOString(),
            ]),
        ]);

        return $ocrTemplate->refresh();
    }

    public function cancel(OcrTemplate $ocrTemplate): OcrTemplate
    {
        $ocrTemplate->update([
            'is_active' => false,
        ]);

        return $ocrTemplate->refresh();
    }

    public function deleteModel(OcrTemplate $ocrTemplate): bool
    {
        return $ocrTemplate->delete();
    }
}