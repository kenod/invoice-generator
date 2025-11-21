<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator\Tests;

use Kenod\InvoiceGenerator\PaymentDetails;
use PHPUnit\Framework\TestCase;

final class PaymentDetailsTest extends TestCase {
	private PaymentDetails $paymentDetails;

	protected function setUp(): void {
		$this->paymentDetails = new PaymentDetails();
	}

	public function testGetPaymentMethodReturnsDefaultValue(): void {
		$paymentMethod = $this->paymentDetails->getPaymentMethod();

		self::assertIsArray($paymentMethod);
		self::assertCount(3, $paymentMethod);
		self::assertSame('Payment method:', $paymentMethod[0]);
		self::assertNull($paymentMethod[1]);
		self::assertFalse($paymentMethod[2]);
	}

	public function testSetPaymentMethodUpdatesValue(): void {
		$this->paymentDetails->setPaymentMethod('Bank transfer');
		$paymentMethod = $this->paymentDetails->getPaymentMethod();

		self::assertSame('Bank transfer', $paymentMethod[1]);
		self::assertFalse($paymentMethod[2]);
	}

	public function testGetAccountNumberReturnsDefaultValue(): void {
		$accountNumber = $this->paymentDetails->getAccountNumber();

		self::assertIsArray($accountNumber);
		self::assertCount(3, $accountNumber);
		self::assertSame('Account number:', $accountNumber[0]);
		self::assertNull($accountNumber[1]);
		self::assertFalse($accountNumber[2]);
	}

	public function testSetAccountNumberUpdatesValue(): void {
		$this->paymentDetails->setAccountNumber('123456789');
		$accountNumber = $this->paymentDetails->getAccountNumber();

		self::assertSame('123456789', $accountNumber[1]);
		self::assertFalse($accountNumber[2]);
	}

	public function testGetBankCodeReturnsDefaultValue(): void {
		$bankCode = $this->paymentDetails->getBankCode();

		self::assertIsArray($bankCode);
		self::assertCount(3, $bankCode);
		self::assertSame('Bank code:', $bankCode[0]);
		self::assertNull($bankCode[1]);
		self::assertFalse($bankCode[2]);
	}

	public function testSetBankCodeUpdatesValue(): void {
		$this->paymentDetails->setBankCode('0100');
		$bankCode = $this->paymentDetails->getBankCode();

		self::assertSame('0100', $bankCode[1]);
		self::assertFalse($bankCode[2]);
	}

	public function testGetVariableSymbolReturnsDefaultValue(): void {
		$variableSymbol = $this->paymentDetails->getVariableSymbol();

		self::assertIsArray($variableSymbol);
		self::assertCount(3, $variableSymbol);
		self::assertSame('Variable symbol:', $variableSymbol[0]);
		self::assertNull($variableSymbol[1]);
		self::assertFalse($variableSymbol[2]);
	}

	public function testSetVariableSymbolUpdatesValue(): void {
		$this->paymentDetails->setVariableSymbol('202400123');
		$variableSymbol = $this->paymentDetails->getVariableSymbol();

		self::assertSame('202400123', $variableSymbol[1]);
		self::assertFalse($variableSymbol[2]);
	}

	public function testGetConstantSymbolReturnsDefaultValue(): void {
		$constantSymbol = $this->paymentDetails->getConstantSymbol();

		self::assertIsArray($constantSymbol);
		self::assertCount(3, $constantSymbol);
		self::assertSame('Constant symbol:', $constantSymbol[0]);
		self::assertNull($constantSymbol[1]);
		self::assertFalse($constantSymbol[2]);
	}

	public function testSetConstantSymbolUpdatesValue(): void {
		$this->paymentDetails->setConstantSymbol('0308');
		$constantSymbol = $this->paymentDetails->getConstantSymbol();

		self::assertSame('0308', $constantSymbol[1]);
		self::assertFalse($constantSymbol[2]);
	}

	public function testGetSpecificSymbolReturnsDefaultValue(): void {
		$specificSymbol = $this->paymentDetails->getSpecificSymbol();

		self::assertIsArray($specificSymbol);
		self::assertCount(3, $specificSymbol);
		self::assertSame('Specific symbol:', $specificSymbol[0]);
		self::assertNull($specificSymbol[1]);
		self::assertFalse($specificSymbol[2]);
	}

	public function testSetSpecificSymbolUpdatesValue(): void {
		$this->paymentDetails->setSpecificSymbol('123');
		$specificSymbol = $this->paymentDetails->getSpecificSymbol();

		self::assertSame('123', $specificSymbol[1]);
		self::assertFalse($specificSymbol[2]);
	}

	public function testGetDynamicPropertiesReturnsEmptyByDefault(): void {
		$dynamicProperties = $this->paymentDetails->getDynamicProperties();

		self::assertIsArray($dynamicProperties);
		self::assertEmpty($dynamicProperties);
	}

	public function testAddParameterAddsToProperties(): void {
		$this->paymentDetails->addParameter('IBAN', 'CZ65 0800 0000 1920 0014 5399');
		$dynamicProperties = $this->paymentDetails->getDynamicProperties();

		self::assertNotEmpty($dynamicProperties);
		self::assertArrayHasKey('parametr1', $dynamicProperties);
		self::assertSame(['IBAN', 'CZ65 0800 0000 1920 0014 5399'], $dynamicProperties['parametr1']);
	}

	public function testAddMultipleParameters(): void {
		$this->paymentDetails
			->addParameter('IBAN', 'CZ65 0800 0000 1920 0014 5399')
			->addParameter('SWIFT', 'GIBACZPX')
			->addParameter('Note', 'Payment for invoice');

		$dynamicProperties = $this->paymentDetails->getDynamicProperties();

		self::assertCount(3, $dynamicProperties);
		self::assertArrayHasKey('parametr1', $dynamicProperties);
		self::assertArrayHasKey('parametr2', $dynamicProperties);
		self::assertArrayHasKey('parametr3', $dynamicProperties);
	}

	public function testAddParameterIgnoresEmptyName(): void {
		$this->paymentDetails->addParameter('', 'Value');
		$dynamicProperties = $this->paymentDetails->getDynamicProperties();

		self::assertEmpty($dynamicProperties);
	}

	public function testAddParameterIgnoresEmptyValue(): void {
		$this->paymentDetails->addParameter('Name', '');
		$dynamicProperties = $this->paymentDetails->getDynamicProperties();

		self::assertEmpty($dynamicProperties);
	}

	public function testSetPaymentMethodIgnoresEmptyString(): void {
		$this->paymentDetails->setPaymentMethod('Cash');
		$this->paymentDetails->setPaymentMethod('');
		$paymentMethod = $this->paymentDetails->getPaymentMethod();

		self::assertSame('Cash', $paymentMethod[1]);
	}

	public function testFluentInterface(): void {
		$result = $this->paymentDetails
			->setPaymentMethod('Bank transfer')
			->setAccountNumber('123456789')
			->setBankCode('0100')
			->setVariableSymbol('202400123')
			->setConstantSymbol('0308')
			->setSpecificSymbol('123')
			->addParameter('IBAN', 'CZ65 0800 0000 1920 0014 5399');

		self::assertSame($this->paymentDetails, $result);
	}

	public function testGetPropertiesIncludesAllFields(): void {
		$this->paymentDetails
			->setPaymentMethod('Bank transfer')
			->setVariableSymbol('202400123')
			->addParameter('IBAN', 'CZ65 0800 0000 1920 0014 5399');

		$properties = $this->paymentDetails->getProperties();

		self::assertArrayHasKey('paymentMethod', $properties);
		self::assertArrayHasKey('variableSymbol', $properties);
		self::assertArrayHasKey('parametr1', $properties);
	}

	public function testGetPropertiesIncludesDynamicProperties(): void {
		$this->paymentDetails
			->addParameter('Custom1', 'Value1')
			->addParameter('Custom2', 'Value2');

		$properties = $this->paymentDetails->getProperties();

		self::assertArrayHasKey('parametr1', $properties);
		self::assertArrayHasKey('parametr2', $properties);
		self::assertSame(['Custom1', 'Value1'], $properties['parametr1']);
		self::assertSame(['Custom2', 'Value2'], $properties['parametr2']);
	}

	public function testTranslateUpdatesLabels(): void {
		$this->paymentDetails->translate();

		$paymentMethod = $this->paymentDetails->getPaymentMethod();
		$accountNumber = $this->paymentDetails->getAccountNumber();

		self::assertIsString($paymentMethod[0]);
		self::assertIsString($accountNumber[0]);
	}
}
