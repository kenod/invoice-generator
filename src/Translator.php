<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator;

/**
 * Handles translation/localization for invoices
 */
final class Translator {
	/**
	 * Loaded translations array
	 *
	 * @var array<string, string>|null
	 */
	protected static ?array $translations = null;

	public static function loadTranslations(string $filePath): void {
		if (!is_file($filePath)) {
			return;
		}

		self::$translations = require $filePath;
	}

	/**
	 * Translates a given text to the loaded language
	 *
	 * @param string $string Text to translate
	 * @return string Translated text or original if translation not found
	 */
	public static function t(string $string): string {
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
	public static function hasLanguage(): bool {
		return self::$translations !== null;
	}

	/**
	 * Modifies loaded translations with custom values
	 *
	 * Existing keys will be overwritten, new keys will be added.
	 *
	 * @param array<string, string> $translations Translations to modify
	 */
	public static function modifyTranslations(array $translations): void {
		if (self::$translations === null) {
			self::$translations = [];
		}

		self::$translations = array_merge(self::$translations, $translations);
	}
}
