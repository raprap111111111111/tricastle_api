<?php

return [
    /*
    |---------------------------------------------------------------
    | Translation Provider
    |---------------------------------------------------------------
    | Supported: "deepl", "google", "libre"
    */
    'provider' => env('TRANSLATION_PROVIDER', 'deepl'),

    /*
    |---------------------------------------------------------------
    | Country → Target Language Map
    |---------------------------------------------------------------
    | Maps country names to ISO 639-1 language codes
    */
    'country_language_map' => [
        'Japan'          => 'ja',
        'South Korea'    => 'ko',
        'Taiwan'         => 'zh',    // Traditional Chinese
        'China'          => 'zh',    // Simplified Chinese
        'Hong Kong'      => 'zh',
        'Singapore'      => 'en',    // English is primary
        'Thailand'       => 'th',
        'Vietnam'        => 'vi',
        'Indonesia'      => 'id',
        'Malaysia'       => 'ms',
        'Philippines'    => 'tl',    // Tagalog / Filipino
        'UAE'            => 'ar',
        'Saudi Arabia'   => 'ar',
        'Germany'        => 'de',
        'France'         => 'fr',
        'Spain'          => 'es',
        'Italy'          => 'it',
        'Portugal'       => 'pt',
        'Russia'         => 'ru',
        'Netherlands'    => 'nl',
        'United States'  => 'en',
        'United Kingdom' => 'en',
        'Canada'         => 'en',
        'Australia'      => 'en',
    ],

    /*
    |---------------------------------------------------------------
    | Provider Credentials
    |---------------------------------------------------------------
    */
    'deepl' => [
        'api_key' => env('DEEPL_API_KEY'),
        'base_url' => env('DEEPL_BASE_URL', 'https://api-free.deepl.com'),
    ],

    'google' => [
        'api_key' => env('GOOGLE_TRANSLATE_API_KEY'),
    ],

    'libre' => [
        'base_url' => env('LIBRE_TRANSLATE_URL', 'https://libretranslate.com'),
        'api_key'  => env('LIBRE_TRANSLATE_API_KEY'),
    ],
];