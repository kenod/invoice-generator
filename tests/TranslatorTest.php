<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator\Tests;

use Kenod\InvoiceGenerator\Translator;
use PHPUnit\Framework\TestCase;

final class TranslatorTest extends TestCase {

	protected function setUp(): void {
		// Reset translations before each test
		$reflection = new \ReflectionClass(Translator::class);
		$property = $reflection->getProperty('translations');
		$property->setAccessible(true);
		$property->setValue(null, null);
	}

	public function testLoadTranslationsFromValidFile(): void {
		$langFile = dirname(__DIR__) . '/langs/cs.php';

		Translator::loadTranslations($langFile);

		self::assertTrue(Translator::hasLanguage());
	}

	public function testLoadTranslationsFromNonExistentFile(): void {
		Translator::loadTranslations('/nonexistent/file.php');

		self::assertFalse(Translator::hasLanguage());
	}

	public function testTranslateExistingKey(): void {
		$langFile = dirname(__DIR__) . '/langs/en.php';
		Translator::loadTranslations($langFile);

		$result = Translator::t('supplier');

		self::assertSame('Supplier', $result);
	}

	public function testTranslateNonExistentKeyReturnsFallback(): void {
		$langFile = dirname(__DIR__) . '/langs/en.php';
		Translator::loadTranslations($langFile);

		$result = Translator::t('nonexistent_key');

		self::assertSame('nonexistent_key', $result);
	}

	public function testTranslateWithoutLoadedLanguageReturnsFallback(): void {
		$result = Translator::t('some_key');

		self::assertSame('some_key', $result);
	}

	public function testHasLanguageReturnsFalseInitially(): void {
		self::assertFalse(Translator::hasLanguage());
	}

	public function testHasLanguageReturnsTrueAfterLoading(): void {
		$langFile = dirname(__DIR__) . '/langs/cs.php';
		Translator::loadTranslations($langFile);

		self::assertTrue(Translator::hasLanguage());
	}


	public function testAllRequiredKeysExistInCzech(): void {
		$langFile = dirname(__DIR__) . '/langs/cs.php';
		Translator::loadTranslations($langFile);

		$requiredKeys = [
			'generated_footer',
			'page',
			'supplier',
			'customer',
			'payment_details',
			'items_to_pay',
			'total_to_pay',
			'vat',
			'vat_rate',
		];

		foreach ($requiredKeys as $key) {
			$translation = Translator::t($key);
			self::assertNotSame($key, $translation, "Key '$key' should have a translation");
		}
	}

	public function testModifyTranslationsOverwritesExistingKeys(): void {
		$langFile = dirname(__DIR__) . '/langs/en.php';
		Translator::loadTranslations($langFile);

		Translator::modifyTranslations([
			'supplier' => 'Custom Supplier',
		]);

		self::assertSame('Custom Supplier', Translator::t('supplier'));
	}

	public function testModifyTranslationsAddsNewKeys(): void {
		$langFile = dirname(__DIR__) . '/langs/en.php';
		Translator::loadTranslations($langFile);

		Translator::modifyTranslations([
			'custom_key' => 'Custom Value',
		]);

		self::assertSame('Custom Value', Translator::t('custom_key'));
	}

	public function testModifyTranslationsPreservesUnchangedKeys(): void {
		$langFile = dirname(__DIR__) . '/langs/en.php';
		Translator::loadTranslations($langFile);

		$originalCustomer = Translator::t('customer');

		Translator::modifyTranslations([
			'supplier' => 'New Supplier',
		]);

		self::assertSame($originalCustomer, Translator::t('customer'));
	}

	public function testModifyTranslationsWithoutLoadedLanguage(): void {
		Translator::modifyTranslations([
			'new_key' => 'New Value',
		]);

		self::assertTrue(Translator::hasLanguage());
		self::assertSame('New Value', Translator::t('new_key'));
	}
}
