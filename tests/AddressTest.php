<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator\Tests;

use Kenod\InvoiceGenerator\Address;
use PHPUnit\Framework\TestCase;

final class AddressTest extends TestCase {
	private Address $address;

	protected function setUp(): void {
		$this->address = new Address();
	}

	public function testGetCompanyReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getCompany());
	}

	public function testSetCompanyUpdatesValue(): void {
		$this->address->setCompany('ACME Corp');

		self::assertSame('ACME Corp', $this->address->getCompany());
	}

	public function testGetNameReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getName());
	}

	public function testSetNameUpdatesValue(): void {
		$this->address->setName('John Doe');

		self::assertSame('John Doe', $this->address->getName());
	}

	public function testGetStreetReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getStreet());
	}

	public function testSetStreetUpdatesValue(): void {
		$this->address->setStreet('Main Street 123');

		self::assertSame('Main Street 123', $this->address->getStreet());
	}

	public function testGetPostalCodeReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getPostalCode());
	}

	public function testSetPostalCodeUpdatesValue(): void {
		$this->address->setPostalCode('110 00');

		self::assertSame('110 00', $this->address->getPostalCode());
	}

	public function testGetCityReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getCity());
	}

	public function testSetCityUpdatesValue(): void {
		$this->address->setCity('Prague');

		self::assertSame('Prague', $this->address->getCity());
	}

	public function testGetCountryReturnsDefaultValue(): void {
		self::assertSame('Czech Republic', $this->address->getCountry());
	}

	public function testSetCountryUpdatesValue(): void {
		$this->address->setCountry('Slovakia');

		self::assertSame('Slovakia', $this->address->getCountry());
	}

	public function testGetCompanyIdReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getCompanyId());
	}

	public function testSetCompanyIdUpdatesValue(): void {
		$this->address->setCompanyId('12345678');

		self::assertSame('12345678', $this->address->getCompanyId());
	}

	public function testGetTaxIdReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getTaxId());
	}

	public function testSetTaxIdUpdatesValue(): void {
		$this->address->setTaxId('CZ12345678');

		self::assertSame('CZ12345678', $this->address->getTaxId());
	}

	public function testGetPhoneReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getPhone());
	}

	public function testSetPhoneUpdatesValue(): void {
		$this->address->setPhone('+420 123 456 789');

		self::assertSame('+420 123 456 789', $this->address->getPhone());
	}

	public function testGetEmailReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getEmail());
	}

	public function testSetEmailUpdatesValue(): void {
		$this->address->setEmail('test@example.com');

		self::assertSame('test@example.com', $this->address->getEmail());
	}

	public function testGetWebReturnsEmptyByDefault(): void {
		self::assertSame('', $this->address->getWeb());
	}

	public function testSetWebUpdatesValue(): void {
		$this->address->setWeb('www.example.com');

		self::assertSame('www.example.com', $this->address->getWeb());
	}

	public function testGetSeparatorReturnsDefaultValue(): void {
		self::assertSame('--', $this->address->getSeparator());
	}

	public function testGetSeparator2ReturnsDefaultValue(): void {
		self::assertSame('--', $this->address->getSeparator2());
	}

	public function testFluentInterface(): void {
		$result = $this->address
			->setCompany('ACME Corp')
			->setName('John Doe')
			->setStreet('Main Street 123')
			->setCity('Prague')
			->setPostalCode('110 00')
			->setCountry('Czech Republic')
			->setCompanyId('12345678')
			->setTaxId('CZ12345678')
			->setPhone('+420 123 456 789')
			->setEmail('test@example.com')
			->setWeb('www.example.com');

		self::assertSame($this->address, $result);
	}

	public function testGetPropertiesReturnsOnlyNonEmptyValues(): void {
		$this->address
			->setCompany('ACME Corp')
			->setCity('Prague');

		$properties = $this->address->getProperties();

		self::assertArrayHasKey('company', $properties);
		self::assertArrayHasKey('city', $properties);
		self::assertArrayHasKey('country', $properties); // Default value
		self::assertArrayHasKey('separator', $properties); // Default value
		self::assertArrayNotHasKey('name', $properties); // Empty
		self::assertArrayNotHasKey('street', $properties); // Empty
	}

	public function testGetPropertiesIncludesSeparators(): void {
		$this->address->setCompany('ACME Corp');

		$properties = $this->address->getProperties();

		self::assertArrayHasKey('separator', $properties);
		self::assertArrayHasKey('separator2', $properties);
	}
}
