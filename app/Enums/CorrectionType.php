<?php

namespace App\Enums;

enum CorrectionType: string
{
    case OCR_MISREAD = 'ocr_misread';
    case WRONG_FIELD = 'wrong_field';
    case MISSED_FIELD = 'missed_field';
    case FORMAT_ERROR = 'format_error';
    case PARTIAL_EXTRACTION = 'partial_extraction';
    case WRONG_LANGUAGE = 'wrong_language';
    case POOR_IMAGE_QUALITY = 'poor_image_quality';
    case TEMPLATE_MISMATCH = 'template_mismatch';
    case PUNCTUATION_ERROR = 'punctuation_error';
    case CASE_ERROR = 'case_error';
    case SPACING_ERROR = 'spacing_error';
    case SPECIAL_CHARACTER = 'special_character';
    case NUMBER_LETTER_CONFUSION = 'number_letter_confusion';
    case SIMILAR_CHARACTER = 'similar_character';
    case HANDWRITTEN_TEXT = 'handwritten_text';
    case STAMP_OR_SEAL = 'stamp_or_seal';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::OCR_MISREAD => 'OCR Misread',
            self::WRONG_FIELD => 'Wrong Field Extracted',
            self::MISSED_FIELD => 'Missed Field',
            self::FORMAT_ERROR => 'Format Error',
            self::PARTIAL_EXTRACTION => 'Partial Extraction',
            self::WRONG_LANGUAGE => 'Language Detection Error',
            self::POOR_IMAGE_QUALITY => 'Poor Image Quality',
            self::TEMPLATE_MISMATCH => 'Template Mismatch',
            self::PUNCTUATION_ERROR => 'Punctuation Error',
            self::CASE_ERROR => 'Case Error',
            self::SPACING_ERROR => 'Spacing Error',
            self::SPECIAL_CHARACTER => 'Special Character Error',
            self::NUMBER_LETTER_CONFUSION => 'Number/Letter Confusion',
            self::SIMILAR_CHARACTER => 'Similar Character Error',
            self::HANDWRITTEN_TEXT => 'Handwritten Text Issue',
            self::STAMP_OR_SEAL => 'Stamp or Seal Overlap',
            self::OTHER => 'Other',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }

    public function isImageRelated(): bool
    {
        return in_array($this, [
            self::POOR_IMAGE_QUALITY,
            self::STAMP_OR_SEAL,
        ]);
    }

    public function isOcrRelated(): bool
    {
        return in_array($this, [
            self::OCR_MISREAD,
            self::WRONG_FIELD,
            self::MISSED_FIELD,
            self::NUMBER_LETTER_CONFUSION,
            self::SIMILAR_CHARACTER,
        ]);
    }
}
