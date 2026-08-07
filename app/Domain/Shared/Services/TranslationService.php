<?php

namespace App\Domain\Shared\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    /**
     * Translate text into the language of the given country.
     *
     * @param  string  $text        Original text (any language)
     * @param  string  $country     Country name (e.g. "Japan")
     * @param  string  $sourceLang  Source ISO code, "auto" for auto-detect
     * @return string|null          Translated text or null on failure
     */
    public function translateForCountry(
        string $text,
        string $country,
        string $sourceLang = 'auto',
    ): ?string {
        if (blank($text)) {
            return null;
        }

        $targetLang = $this->getLanguageForCountry($country);

        if (!$targetLang) {
            return null;
        }

        return $this->translate($text, $targetLang, $sourceLang);
    }

    /**
     * Get ISO language code for a country name.
     */
    public function getLanguageForCountry(string $country): ?string
    {
        $map = config('translation.country_language_map', []);
        return $map[$country] ?? null;
    }

    /**
     * Check if the target country's language differs from English.
     * Useful to skip translation for English-speaking countries.
     */
    public function needsTranslation(string $country): bool
    {
        $lang = $this->getLanguageForCountry($country);
        return $lang !== null && $lang !== 'en';
    }

    /**
     * Translate to a specific language (cached 30 days).
     */
    public function translate(
        string $text,
        string $targetLang,
        string $sourceLang = 'auto',
    ): ?string {
        $cacheKey = "translation:{$sourceLang}:{$targetLang}:" . md5($text);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($text, $targetLang, $sourceLang) {
            $provider = config('translation.provider', 'deepl');

            try {
                return match ($provider) {
                    'deepl'  => $this->translateDeepL($text, $targetLang, $sourceLang),
                    'google' => $this->translateGoogle($text, $targetLang, $sourceLang),
                    'libre'  => $this->translateLibre($text, $targetLang, $sourceLang),
                    default  => null,
                };
            } catch (\Throwable $e) {
                Log::warning('[TranslationService] Failed to translate', [
                    'provider' => $provider,
                    'text'     => $text,
                    'target'   => $targetLang,
                    'error'    => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    // ─── DeepL (best for JP/KR/ZH) ────────────────────────────────
    protected function translateDeepL(string $text, string $target, string $source): ?string
    {
        $apiKey  = config('translation.deepl.api_key');
        $baseUrl = config('translation.deepl.base_url');

        if (!$apiKey) return null;

        $response = Http::withHeaders([
            'Authorization' => "DeepL-Auth-Key {$apiKey}",
        ])
        ->asForm()
        ->post("{$baseUrl}/v2/translate", array_filter([
            'text'        => $text,
            'target_lang' => strtoupper($target),
            'source_lang' => $source === 'auto' ? null : strtoupper($source),
        ]));

        if (!$response->successful()) return null;
        return $response->json('translations.0.text');
    }

    // ─── Google Translate ─────────────────────────────────────────
    protected function translateGoogle(string $text, string $target, string $source): ?string
    {
        $apiKey = config('translation.google.api_key');
        if (!$apiKey) return null;

        $response = Http::post('https://translation.googleapis.com/language/translate/v2', [
            'q'      => $text,
            'target' => $target,
            'source' => $source === 'auto' ? null : $source,
            'format' => 'text',
            'key'    => $apiKey,
        ]);

        if (!$response->successful()) return null;
        return $response->json('data.translations.0.translatedText');
    }

    // ─── LibreTranslate (free, self-hostable) ─────────────────────
    protected function translateLibre(string $text, string $target, string $source): ?string
    {
        $baseUrl = config('translation.libre.base_url');
        $apiKey  = config('translation.libre.api_key');

        $response = Http::post("{$baseUrl}/translate", array_filter([
            'q'       => $text,
            'source'  => $source === 'auto' ? 'auto' : $source,
            'target'  => $target,
            'format'  => 'text',
            'api_key' => $apiKey,
        ]));

        if (!$response->successful()) return null;
        return $response->json('translatedText');
    }
}