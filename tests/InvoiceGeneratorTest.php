<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator\Tests;

use Kenod\InvoiceGenerator\InvoiceGenerator;
use PHPUnit\Framework\TestCase;

final class InvoiceGeneratorTest extends TestCase {

	public function testBasicInvoiceGeneration(): void {
		$invoice = new InvoiceGenerator();

		// Basic settings
		$invoice->getSettings()
			->setInvoiceNumber('TEST001')
			->setVatPayer(false);

		// Supplier
		$invoice->getSupplier()
			->setName('Test Company')
			->setStreet('Test Street 123')
			->setCity('Prague')
			->setPostalCode('110 00');

		// Customer
		$invoice->getCustomer()
			->setCompany('Customer Ltd')
			->setStreet('Customer St 456')
			->setCity('Brno')
			->setPostalCode('602 00');

		// Information
		$invoice->getInformation()
			->setIssueDate('2024-01-20')
			->setDueDate('2024-02-05');

		// Payment
		$invoice->getPaymentDetails()
			->setPaymentMethod('Bank transfer')
			->setAccountNumber('123456/0100')
			->setVariableSymbol('TEST001');

		// Add items
		$invoice->addItem('Service A', 10, 1000, 'hours', 0);
		$invoice->addItem('Service B', 5, 2000, 'hours', 0);

		// Generate PDF (without output)
		$result = $invoice->generate(false);

		self::assertTrue($result);
		self::assertGreaterThan(0, $invoice->getTotalPrice());
	}

	public function testPriceCalculationNonVatPayer(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()->setVatPayer(false);

		$invoice->addItem('Item 1', 2, 100, 'pcs', 0);  // 200
		$invoice->addItem('Item 2', 3, 50, 'pcs', 0);   // 150

		$prices = $invoice->getFinalPrices();

		self::assertArrayHasKey('total', $prices);
		self::assertSame(350.0, $prices['total']);
		self::assertArrayNotHasKey('vat_summary', $prices);
	}

	public function testPriceCalculationVatPayer(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setVatPayer(true)
			->setRounding(0, 0, 0, 0, false);  // No rounding

		// Item: 100 * 2 = 200 base, VAT 21% = 42, total = 242
		$invoice->addItem('Item with VAT 21%', 2, 100, 'pcs', 21);

		// Item: 50 * 3 = 150 base, VAT 15% = 22.5, total = 172.5
		$invoice->addItem('Item with VAT 15%', 3, 50, 'pcs', 15);

		$prices = $invoice->getFinalPrices();

		self::assertArrayHasKey('total', $prices);
		self::assertArrayHasKey('vat_summary', $prices);

		// Total should be 242 + 172.5 = 414.5
		self::assertSame(414.5, $prices['total']);

		// Check VAT summary for 21%
		self::assertArrayHasKey('21', $prices['vat_summary']);
		self::assertSame(200.0, $prices['vat_summary']['21']['base']);
		self::assertSame(42.0, $prices['vat_summary']['21']['vat']);
		self::assertSame(242.0, $prices['vat_summary']['21']['total']);

		// Check VAT summary for 15%
		self::assertArrayHasKey('15', $prices['vat_summary']);
		self::assertSame(150.0, $prices['vat_summary']['15']['base']);
		self::assertSame(22.5, $prices['vat_summary']['15']['vat']);
		self::assertSame(172.5, $prices['vat_summary']['15']['total']);
	}

	public function testPriceCalculationWithDiscount(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setVatPayer(false)
			->setDiscount(10, 1, 0, 0, 0);  // 10% discount

		$invoice->addItem('Item 1', 1, 1000, 'pcs', 0);  // 1000

		$prices = $invoice->getFinalPrices();

		// 1000 - 10% = 900
		self::assertSame(900.0, $prices['total']);
	}

	public function testPriceCalculationWithFixedDiscount(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setVatPayer(false)
			->setDiscount(50, 0, 0, 0, 0);  // Fixed 50 CZK discount

		$invoice->addItem('Item 1', 1, 1000, 'pcs', 0);  // 1000

		$prices = $invoice->getFinalPrices();

		// 1000 - 50 = 950
		self::assertSame(950.0, $prices['total']);
	}

	public function testPriceCalculationWithDeposits(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setVatPayer(false)
			->setDeposits(300);

		$invoice->addItem('Item 1', 1, 1000, 'pcs', 0);  // 1000

		$prices = $invoice->getFinalPrices();

		// 1000 - 300 (deposit) = 700
		self::assertSame(700.0, $prices['total']);
	}

	public function testPriceCalculationWithRounding(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setVatPayer(false)
			->setRounding(1, 1, 1, 0, false);  // Round to whole crowns

		$invoice->addItem('Item 1', 1, 1234.56, 'pcs', 0);  // 1234.56

		$prices = $invoice->getFinalPrices();

		// Should round to 1235.0
		self::assertSame(1235.0, $prices['total']);
	}

	public function testGetTotalPriceAfterGeneration(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setInvoiceNumber('TEST002')
			->setVatPayer(true);

		$invoice->getSupplier()
			->setName('Test Supplier')
			->setCity('Prague');

		$invoice->getCustomer()
			->setCompany('Test Customer')
			->setCity('Brno');

		$invoice->getInformation()
			->setIssueDate('2024-01-01')
			->setDueDate('2024-01-15');

		$invoice->getPaymentDetails()
			->setPaymentMethod('Cash');

		$invoice->addItem('Product', 5, 200, 'pcs', 21);

		// Generate
		$invoice->generate(false);

		$totalPrice = $invoice->getTotalPrice();

		// 5 * 200 = 1000 base + 21% VAT = 1210
		self::assertSame(1210.0, $totalPrice);
	}

	public function testReverseChargeVat(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setVatPayer(true)
			->setReverseCharge(true);

		$invoice->addItem('Service', 1, 1000, 'hours', 21);

		$prices = $invoice->getFinalPrices();

		// In reverse charge, VAT should be 0
		self::assertArrayHasKey('21', $prices['vat_summary']);
		self::assertSame(1000.0, $prices['vat_summary']['21']['base']);
		self::assertSame(0.0, $prices['vat_summary']['21']['vat']);
		self::assertSame(1000.0, $prices['vat_summary']['21']['total']);
	}

	public function testComplexInvoiceWithMultipleVatRates(): void {
		$invoice = new InvoiceGenerator();

		$invoice->getSettings()
			->setVatPayer(true)
			->setRounding(0, 0, 0, 0, false)
			->setDiscount(5, 1, 0, 0, 0);  // 5% discount

		// Add items with different VAT rates
		$invoice->addItem('Item 21%', 10, 100, 'pcs', 21);    // 1000 base -> 1210 total
		$invoice->addItem('Item 15%', 5, 200, 'pcs', 15);     // 1000 base -> 1150 total
		$invoice->addItem('Item 10%', 2, 500, 'pcs', 10);     // 1000 base -> 1100 total

		$prices = $invoice->getFinalPrices();

		// Total before discount: 1210 + 1150 + 1100 = 3460
		// After 5% discount: 3460 * 0.95 = 3287
		self::assertSame(3287.0, $prices['total']);

		// Verify all VAT rates are present
		self::assertArrayHasKey('21', $prices['vat_summary']);
		self::assertArrayHasKey('15', $prices['vat_summary']);
		self::assertArrayHasKey('10', $prices['vat_summary']);
	}
}
