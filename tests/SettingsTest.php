<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator\Tests;

use Kenod\InvoiceGenerator\Settings;
use PHPUnit\Framework\TestCase;

final class SettingsTest extends TestCase {
	private Settings $settings;

	protected function setUp(): void {
		$this->settings = new Settings();
	}

	public function testDefaultCurrency(): void {
		self::assertSame('CZK', $this->settings->getCurrency());
	}

	public function testSetInvoiceNumberReturnsThis(): void {
		$result = $this->settings->setInvoiceNumber('2024001');

		self::assertSame($this->settings, $result);
		self::assertSame('2024001', $this->settings->getInvoiceNumber());
	}

	public function testSetLanguageLoadsCzech(): void {
		$result = $this->settings->setLanguage('cs');

		self::assertSame($this->settings, $result);
	}

	public function testSetRounding(): void {
		$result = $this->settings->setRounding(1, 2, 3, 4, true);

		self::assertSame($this->settings, $result);
		self::assertSame(1, $this->settings->getRoundTo());
	}

	public function testSetVatPayer(): void {
		$result = $this->settings->setVatPayer(true);

		self::assertSame($this->settings, $result);
		self::assertTrue($this->settings->getVatPayer());
	}
}
