<?php

declare(strict_types=1);

namespace WFP\Invoice;

/**
 * Handles translation/localization for invoices
 */
class Translation
{
    /**
     * Loaded translations array
     *
     * @var array<string, string>|null
     */
    public static ?array $translations = null;

    /**
     * Translates given text to the loaded language
     *
     * @param string $string Text to translate
     * @return string Translated text or original if translation not found
     */
    public static function t(string $string): string
    {
        if (isset(self::$translations[$string])) {
            return self::$translations[$string];
        }

        return $string;
    }

    /**
     * Checks if a language is loaded
     *
     * @return bool True if language is loaded
     */
    public static function hasLanguage(): bool
    {
        return self::$translations !== null;
    }
}
