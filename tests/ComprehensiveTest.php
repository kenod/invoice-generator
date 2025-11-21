<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator\Tests;

use Kenod\InvoiceGenerator\InvoiceGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Comprehensive test to verify all features work together
 * and generate a valid PDF document.
 */
final class ComprehensiveTest extends TestCase
{
	private string $testPdfPath;

	protected function setUp(): void
	{
		$this->testPdfPath = __DIR__ . '/test_comprehensive.pdf';

		// Clean up any existing test file
		if (file_exists($this->testPdfPath)) {
			unlink($this->testPdfPath);
		}
	}

	protected function tearDown(): void
	{
		// Clean up test file after test
		if (file_exists($this->testPdfPath)) {
			unlink($this->testPdfPath);
		}
	}

	public function testComprehensiveInvoiceWithAllFeatures(): void
	{
		$invoice = new InvoiceGenerator();

		// ============================================
		// SETTINGS - Test all setter methods
		// ============================================
		$invoice->getSettings()
			->setInvoiceNumber('TEST-2024/999')
			->setLanguage('cs')
			->setDocumentType(1)
			->setDocumentName('TEST FAKTURA')
			->setVatPayer(true)
			->setCurrency('CZK')
			->setAuthor('PHPUnit Test')
			->setTitle('Comprehensive Test Invoice')
			->setFont('FreeSans')
			->setFilename($this->testPdfPath)
			->setOutputType('F')
			->setSendEmail(false)
			->setVatSummary(true)
			->setAmountsWithVat(false)
			->setDisplayItemCount(true)
			->setDisplayUnitColumn(true)
			->setDisplayedColumns(true, true, true)
			->setNotePosition('top')
			->setSummaryEmpty(false)
			->setItemSpacing(1.5)
			->setAlreadyPaidInPaymentInfo(true)
			->setFinalRecipientDisplay(true)
			->setFinalRecipientDifferentAddress(true);

		// Style settings
		$invoice->getSettings()
			->setStyle('fillColor', 'E8F4F8')
			->setStyle('fontColor', '003D5C')
			->setStyle('pricesFillColor', 'D0E8F0')
			->setStyle('pricesFontColor', '003D5C')
			->setStyle('itemFillColor', 'F5F9FA')
			->setStyle('itemFontColor', '000000')
			->setStyle('signatureFillColor', 'E8F4F8')
			->setStyle('signatureFontColor', '003D5C');

		// Borders
		$invoice->getSettings()->setBorders(true, 0.3, '003D5C', 0);

		// Underline
		$invoice->getSettings()->setUnderline(['color' => '003D5C', 'width' => 0.2]);

		// VAT rates
		$invoice->getSettings()->setVatRates([
			'0' => 'Zero VAT (0%)',
			'12' => 'Reduced VAT (12%)',
			'21' => 'Standard VAT (21%)',
		]);

		// Discount
		$invoice->getSettings()->setDiscount(10, 100, 2, 50, 21);

		// Rounding
		$invoice->getSettings()->setRounding(1, 1, 1, 4, true);

		// Deposits
		$invoice->getSettings()->setDeposits(1000);

		// QR Payment
		$invoice->getSettings()->setQRPayment(true, 20, 3, 'PA', 30, 5);

		// Barcode
		$invoice->getSettings()->setBarcode(999888777, 10, 1, 40, 12);

		// Signature texts
		$invoice->getSettings()
			->setSignatureText('Signature line 1', 9, 'B')
			->setSignatureText('Signature line 2', 8, 'I')
			->setSignatureText('Signature line 3', 8);

		// Footer texts
		$invoice->getSettings()
			->setFooterText('Footer line 1', 8, 'B')
			->setFooterText('Footer line 2', 7, 'I')
			->setFooterText('Footer line 3', 7);

		// EET data
		$invoice->getSettings()->setEET([
			'fik' => 'test-fik-12345',
			'pkp' => 'TEST_PKP',
			'bkp' => 'AABB-CCDD-EEFF-GGHH-IIJJ',
			'rezim' => 'Test',
			'pokladna' => '1',
			'provozovna' => '1',
			'datum' => time(),
		]);

		// ============================================
		// SUPPLIER - Test all setter methods
		// ============================================
		$invoice->getSupplier()
			->setCompany('Test Supplier Company Ltd.')
			->setName('Supplier Name')
			->setStreet('Test Street 123')
			->setPostalCode('110 00')
			->setCity('Prague')
			->setCountry('Czech Republic')
			->setCompanyId('12345678')
			->setTaxId('CZ12345678')
			->setPhone('+420 123 456 789')
			->setEmail('supplier@test.cz')
			->setWeb('www.supplier-test.cz');

		// Verify supplier getters
		self::assertSame('Test Supplier Company Ltd.', $invoice->getSupplier()->getCompany());
		self::assertSame('Supplier Name', $invoice->getSupplier()->getName());
		self::assertSame('Test Street 123', $invoice->getSupplier()->getStreet());
		self::assertSame('110 00', $invoice->getSupplier()->getPostalCode());
		self::assertSame('Prague', $invoice->getSupplier()->getCity());
		self::assertSame('Czech Republic', $invoice->getSupplier()->getCountry());
		self::assertSame('12345678', $invoice->getSupplier()->getCompanyId());
		self::assertSame('CZ12345678', $invoice->getSupplier()->getTaxId());
		self::assertSame('+420 123 456 789', $invoice->getSupplier()->getPhone());
		self::assertSame('supplier@test.cz', $invoice->getSupplier()->getEmail());
		self::assertSame('www.supplier-test.cz', $invoice->getSupplier()->getWeb());

		// ============================================
		// CUSTOMER - Test all setter methods
		// ============================================
		$invoice->getCustomer()
			->setCompany('Test Customer Company Ltd.')
			->setName('Customer Name')
			->setStreet('Customer Street 456')
			->setPostalCode('602 00')
			->setCity('Brno')
			->setCountry('Czech Republic')
			->setCompanyId('87654321')
			->setTaxId('CZ87654321')
			->setPhone('+420 987 654 321')
			->setEmail('customer@test.cz')
			->setWeb('www.customer-test.cz');

		// Verify customer getters
		self::assertSame('Test Customer Company Ltd.', $invoice->getCustomer()->getCompany());
		self::assertSame('Customer Name', $invoice->getCustomer()->getName());

		// ============================================
		// END RECIPIENT - Test all setter methods
		// ============================================
		$invoice->getEndRecipient()
			->setCompany('End Recipient Ltd.')
			->setName('End Recipient Name')
			->setStreet('End Street 789')
			->setPostalCode('702 00')
			->setCity('Ostrava')
			->setCountry('Czech Republic')
			->setCompanyId('11122233')
			->setTaxId('CZ11122233')
			->setPhone('+420 111 222 333')
			->setEmail('endrecipient@test.cz')
			->setWeb('www.end-recipient.cz');

		// Verify end recipient getters
		self::assertSame('End Recipient Ltd.', $invoice->getEndRecipient()->getCompany());
		self::assertSame('End Recipient Name', $invoice->getEndRecipient()->getName());

		// ============================================
		// INFORMATION - Test all setter methods
		// ============================================
		$invoice->getInformation()
			->setOrder('ORD-2024/999')
			->setFromDate('01.01.2024')
			->setIssueDate('15.11.2024')
			->setDueDate('29.11.2024')
			->setTaxableSupplyDate('15.11.2024')
			->addParameter('Param1:', 'Value1')
			->addParameter('Param2:', 'Value2')
			->addParameter('Param3:', 'Value3');

		// Verify information getters (they return arrays with [label, value, display])
		$orderData = $invoice->getInformation()->getOrder();
		self::assertIsArray($orderData);
		self::assertSame('ORD-2024/999', $orderData[1]); // Order value is at index 1

		$fromDate = $invoice->getInformation()->getFromDate();
		self::assertIsArray($fromDate);
		self::assertSame('01.01.2024', $fromDate[1]); // Date value is at index 1

		$issueDate = $invoice->getInformation()->getIssueDate();
		self::assertIsArray($issueDate);
		self::assertSame('15.11.2024', $issueDate[1]);

		$dueDate = $invoice->getInformation()->getDueDate();
		self::assertIsArray($dueDate);
		self::assertSame('29.11.2024', $dueDate[1]);

		$taxableSupplyDate = $invoice->getInformation()->getTaxableSupplyDate();
		self::assertIsArray($taxableSupplyDate);
		self::assertSame('15.11.2024', $taxableSupplyDate[1]);

		// ============================================
		// PAYMENT DETAILS - Test all setter methods
		// ============================================
		$invoice->getPaymentDetails()
			->setPaymentMethod('Bank Transfer')
			->setAccountNumber('123456789/0100')
			->setBankCode('0100')
			->setVariableSymbol('2024999')
			->setConstantSymbol('0308')
			->setSpecificSymbol('999888')
			->addParameter('IBAN:', 'CZ65 0800 0000 1234 5678 9012')
			->addParameter('SWIFT:', 'KOMBCZPP')
			->addParameter('Bank:', 'Test Bank')
			->addParameter('Note:', 'Test payment note');

		// Verify payment details getters (they return arrays with [label, value, display])
		$paymentMethod = $invoice->getPaymentDetails()->getPaymentMethod();
		self::assertIsArray($paymentMethod);
		self::assertSame('Bank Transfer', $paymentMethod[1]);

		$accountNumber = $invoice->getPaymentDetails()->getAccountNumber();
		self::assertIsArray($accountNumber);
		self::assertSame('123456789/0100', $accountNumber[1]);

		$bankCode = $invoice->getPaymentDetails()->getBankCode();
		self::assertIsArray($bankCode);
		self::assertSame('0100', $bankCode[1]);

		$variableSymbol = $invoice->getPaymentDetails()->getVariableSymbol();
		self::assertIsArray($variableSymbol);
		self::assertSame('2024999', $variableSymbol[1]);

		$constantSymbol = $invoice->getPaymentDetails()->getConstantSymbol();
		self::assertIsArray($constantSymbol);
		self::assertSame('0308', $constantSymbol[1]);

		$specificSymbol = $invoice->getPaymentDetails()->getSpecificSymbol();
		self::assertIsArray($specificSymbol);
		self::assertSame('999888', $specificSymbol[1]);

		// ============================================
		// ITEMS - Test all parameters
		// ============================================
		$invoice->addItem('Item 1 - Standard VAT', 10, 1000, 'pcs', 21, 'Note for item 1');
		$invoice->addItem('Item 2 - Reduced VAT', 5, 500, 'hours', 12, 'Note for item 2');
		$invoice->addItem('Item 3 - Zero VAT', 1, 100, 'month', 0, 'Note for item 3');
		$invoice->addItem('Item 4 - No note', 2.5, 250.75, 'kg', 21);
		$invoice->addItem('Item 5 - Large quantity', 1000, 1.50, 'm', 21);

		// Items are added successfully (no getter to verify, but they'll be used in PDF generation)

		// ============================================
		// CUSTOM CALLBACK
		// ============================================
		$callbackExecuted = false;
		$invoice->setCustomCallback(function ($pdf) use (&$callbackExecuted) {
			$callbackExecuted = true;
			$pdf->SetFont('FreeSans', 'B', 8);
			$pdf->Text(10, 280, 'PHPUnit Test Callback');
		});

		// ============================================
		// VERIFY SETTINGS GETTERS (only those that exist)
		// ============================================
		self::assertSame('TEST-2024/999', $invoice->getSettings()->getInvoiceNumber());
		self::assertSame(1, $invoice->getSettings()->getDocumentType());
		self::assertSame('TEST FAKTURA', $invoice->getSettings()->getDocumentName());
		self::assertTrue($invoice->getSettings()->getVatPayer());
		self::assertSame('CZK', $invoice->getSettings()->getCurrency());
		self::assertSame('PHPUnit Test', $invoice->getSettings()->getAuthor());
		self::assertSame('Comprehensive Test Invoice', $invoice->getSettings()->getTitle());
		self::assertSame('FreeSans', $invoice->getSettings()->getFont());
		self::assertTrue($invoice->getSettings()->getVatSummary());
		self::assertFalse($invoice->getSettings()->getAmountsWithVat());
		self::assertTrue($invoice->getSettings()->getDisplayItemCount());
		self::assertSame('top', $invoice->getSettings()->getNotePosition());
		self::assertFalse($invoice->getSettings()->getSummaryEmpty());
		self::assertSame(1.5, $invoice->getSettings()->getItemSpacing());
		self::assertTrue($invoice->getSettings()->getAlreadyPaidInPaymentInfo());

		// Verify arrays are returned for complex getters
		$finalRecipient = $invoice->getSettings()->getFinalRecipient();
		self::assertIsArray($finalRecipient);
		self::assertTrue($finalRecipient['display']);
		self::assertTrue($finalRecipient['different_address']);

		// ============================================
		// GENERATE PDF
		// ============================================
		$invoice->generate();

		// Verify PDF was created
		self::assertFileExists($this->testPdfPath, 'PDF file should be created');

		// Verify file is not empty
		$fileSize = filesize($this->testPdfPath);
		self::assertGreaterThan(0, $fileSize, 'PDF file should not be empty');

		// Verify file is a valid PDF (starts with %PDF)
		$fileHandle = fopen($this->testPdfPath, 'r');
		$header = fread($fileHandle, 4);
		fclose($fileHandle);
		self::assertSame('%PDF', $header, 'File should start with PDF header');

		// Verify callback was executed
		self::assertTrue($callbackExecuted, 'Custom callback should be executed');

		// Verify file size is reasonable (comprehensive invoice should be larger)
		self::assertGreaterThan(50000, $fileSize, 'Comprehensive PDF should be at least 50KB');
	}

	public function testInvoiceItemWithAllSetters(): void
	{
		// Test InvoiceItem class directly with all setters
		// Constructor: name, quantity, price, unit, vat, special, ean, note
		$item = new \Kenod\InvoiceGenerator\InvoiceItem('Test Item', 1, 100, 'pcs', 21, false, null, 'Test note');

		// Verify initial values
		self::assertSame('Test Item', $item->getName());
		self::assertSame(1.0, $item->getQuantity());
		self::assertSame(100.0, $item->getPrice());
		self::assertSame('pcs', $item->getUnit());
		self::assertSame(21.0, $item->getVat());
		self::assertSame('Test note', $item->getNote());
		self::assertFalse($item->getSpecial());
		self::assertNull($item->getEan());

		// Test all setters
		$item->setName('Updated Name');
		$item->setQuantity(5.5);
		$item->setUnit('kg');
		$item->setPrice(250.75);
		$item->setVat(12);
		$item->setSpecial(true);
		$item->setEan(1234567890123);
		$item->setNote('Updated note');

		// Verify all getters after updates
		self::assertSame('Updated Name', $item->getName());
		self::assertSame(5.5, $item->getQuantity());
		self::assertSame('kg', $item->getUnit());
		self::assertSame(250.75, $item->getPrice());
		self::assertSame(12.0, $item->getVat());
		self::assertTrue($item->getSpecial());
		self::assertSame(1234567890123, $item->getEan());
		self::assertSame('Updated note', $item->getNote());
	}

	public function testClearDataMethod(): void
	{
		$invoice = new InvoiceGenerator();

		// Configure first invoice
		$invoice->getSettings()
			->setInvoiceNumber('CLEAR-001')
			->setVatPayer(true)
			->setCurrency('CZK')
			->setFilename($this->testPdfPath)
			->setOutputType('F');

		$invoice->getSupplier()
			->setCompany('First Supplier')
			->setStreet('Test')
			->setCity('Test')
			->setPostalCode('110 00')
			->setCompanyId('12345678')
			->setTaxId('CZ12345678');

		$invoice->getCustomer()
			->setCompany('First Customer')
			->setStreet('Test')
			->setCity('Test')
			->setPostalCode('110 00')
			->setCompanyId('87654321')
			->setTaxId('CZ87654321');

		$invoice->getInformation()
			->setIssueDate('15.11.2024')
			->setDueDate('29.11.2024')
			->setTaxableSupplyDate('15.11.2024');

		$invoice->getPaymentDetails()
			->setPaymentMethod('Cash')
			->setAccountNumber('123/0100')
			->setVariableSymbol('001');

		$invoice->addItem('First Item', 1, 100, 'pcs', 21);

		// Verify first invoice settings
		self::assertSame('CLEAR-001', $invoice->getSettings()->getInvoiceNumber());
		self::assertSame('First Supplier', $invoice->getSupplier()->getCompany());
		self::assertSame('First Customer', $invoice->getCustomer()->getCompany());

		// Generate first invoice
		$invoice->generate(false);

		// Clear data (keeps supplier, customer, end recipient but clears items and some settings)
		$invoice->clearData();

		// Configure second invoice
		$invoice->getSettings()->setInvoiceNumber('CLEAR-002');
		$invoice->getCustomer()->setCompany('Second Customer');
		$invoice->getInformation()->setIssueDate('20.11.2024');
		$invoice->addItem('Second Item', 2, 200, 'pcs', 21);

		// Verify data was updated
		self::assertSame('CLEAR-002', $invoice->getSettings()->getInvoiceNumber());
		self::assertSame('First Supplier', $invoice->getSupplier()->getCompany()); // Supplier kept
		self::assertSame('Second Customer', $invoice->getCustomer()->getCompany()); // Updated

		$issueDate = $invoice->getInformation()->getIssueDate();
		self::assertIsArray($issueDate);
		self::assertSame('20.11.2024', $issueDate[1]); // Updated

		// Generate second invoice and output
		$invoice->generate(false);
		$invoice->outputPDF();

		// Verify PDF was created with multiple invoices
		self::assertFileExists($this->testPdfPath);
	}

	public function testGetFinalPricesMethod(): void
	{
		$invoice = new InvoiceGenerator();

		// Configure invoice
		$invoice->getSettings()
			->setInvoiceNumber('PRICES-001')
			->setVatPayer(true)
			->setCurrency('CZK');

		$invoice->getSupplier()
			->setCompany('Test')
			->setStreet('Test')
			->setCity('Test')
			->setPostalCode('110 00')
			->setCompanyId('12345678')
			->setTaxId('CZ12345678');

		$invoice->getCustomer()
			->setCompany('Test')
			->setStreet('Test')
			->setCity('Test')
			->setPostalCode('602 00')
			->setCompanyId('87654321')
			->setTaxId('CZ87654321');

		$invoice->getInformation()
			->setIssueDate('15.11.2024')
			->setDueDate('29.11.2024')
			->setTaxableSupplyDate('15.11.2024');

		$invoice->getPaymentDetails()
			->setPaymentMethod('Cash')
			->setAccountNumber('123/0100')
			->setVariableSymbol('001');

		// Add items
		$invoice->addItem('Item 1', 10, 100, 'pcs', 21); // 1000 base, 210 VAT, 1210 total
		$invoice->addItem('Item 2', 5, 200, 'pcs', 21);  // 1000 base, 210 VAT, 1210 total

		// Get final prices
		$prices = $invoice->getFinalPrices();

		// Verify prices array structure
		self::assertIsArray($prices);
		self::assertArrayHasKey('total', $prices);
		self::assertArrayHasKey('vat_summary', $prices);

		// Verify we have VAT summary for rate 21
		self::assertArrayHasKey('21', $prices['vat_summary']);
		self::assertArrayHasKey('base', $prices['vat_summary']['21']);
		self::assertArrayHasKey('vat', $prices['vat_summary']['21']);
		self::assertArrayHasKey('total', $prices['vat_summary']['21']);

		// Verify calculations: 10*100 + 5*200 = 2000 base, 420 VAT, 2420 total
		self::assertSame(2000.0, $prices['vat_summary']['21']['base']);
		self::assertSame(420.0, $prices['vat_summary']['21']['vat']);
		self::assertSame(2420.0, $prices['vat_summary']['21']['total']);
		self::assertSame(2420.0, $prices['total']); // Total with VAT
	}
}
