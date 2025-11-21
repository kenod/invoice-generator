<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator\Tests;

use Kenod\InvoiceGenerator\Information;
use PHPUnit\Framework\TestCase;

final class InformationTest extends TestCase {
	private Information $information;

	protected function setUp(): void {
		$this->information = new Information();
	}

	public function testGetOrderReturnsDefaultValue(): void {
		$order = $this->information->getOrder();

		self::assertIsArray($order);
		self::assertCount(3, $order);
		self::assertNull($order[1]);
		self::assertFalse($order[2]);
	}

	public function testSetOrderUpdatesValue(): void {
		$this->information->setOrder('PO-2024-001');
		$order = $this->information->getOrder();

		self::assertSame('PO-2024-001', $order[1]);
	}

	public function testGetFromDateReturnsDefaultValue(): void {
		$fromDate = $this->information->getFromDate();

		self::assertIsArray($fromDate);
		self::assertNull($fromDate[1]);
	}

	public function testSetFromDateUpdatesValue(): void {
		$this->information->setFromDate('2024-01-01');
		$fromDate = $this->information->getFromDate();

		self::assertSame('2024-01-01', $fromDate[1]);
	}

	public function testGetIssueDateReturnsDefaultValue(): void {
		$issueDate = $this->information->getIssueDate();

		self::assertIsArray($issueDate);
		self::assertNull($issueDate[1]);
	}

	public function testSetIssueDateUpdatesValue(): void {
		$this->information->setIssueDate('2024-03-15');
		$issueDate = $this->information->getIssueDate();

		self::assertSame('2024-03-15', $issueDate[1]);
	}

	public function testGetDueDateReturnsDefaultValue(): void {
		$dueDate = $this->information->getDueDate();

		self::assertIsArray($dueDate);
		self::assertNull($dueDate[1]);
	}

	public function testSetDueDateUpdatesValue(): void {
		$this->information->setDueDate('2024-03-30');
		$dueDate = $this->information->getDueDate();

		self::assertSame('2024-03-30', $dueDate[1]);
	}

	public function testGetTaxableSupplyDateReturnsDefaultValue(): void {
		$taxableSupplyDate = $this->information->getTaxableSupplyDate();

		self::assertIsArray($taxableSupplyDate);
		self::assertNull($taxableSupplyDate[1]);
		self::assertTrue($taxableSupplyDate[2]); // Default visibility is true
	}

	public function testSetTaxableSupplyDateUpdatesValue(): void {
		$this->information->setTaxableSupplyDate('2024-03-15');
		$taxableSupplyDate = $this->information->getTaxableSupplyDate();

		self::assertSame('2024-03-15', $taxableSupplyDate[1]);
		self::assertTrue($taxableSupplyDate[2]);
	}

	public function testGetDynamicPropertiesReturnsEmptyByDefault(): void {
		$dynamicProperties = $this->information->getDynamicProperties();

		self::assertIsArray($dynamicProperties);
		self::assertEmpty($dynamicProperties);
	}

	public function testAddParameterAddsToProperties(): void {
		$this->information->addParameter('Project', 'Website Development');
		$dynamicProperties = $this->information->getDynamicProperties();

		self::assertNotEmpty($dynamicProperties);
		self::assertArrayHasKey('parametr1', $dynamicProperties);
		self::assertSame(['Project', 'Website Development'], $dynamicProperties['parametr1']);
	}

	public function testAddMultipleParameters(): void {
		$this->information
			->addParameter('Project', 'Website Development')
			->addParameter('Client', 'ACME Corp')
			->addParameter('Reference', 'REF-2024-001');

		$dynamicProperties = $this->information->getDynamicProperties();

		self::assertCount(3, $dynamicProperties);
		self::assertArrayHasKey('parametr1', $dynamicProperties);
		self::assertArrayHasKey('parametr2', $dynamicProperties);
		self::assertArrayHasKey('parametr3', $dynamicProperties);
	}

	public function testAddParameterIgnoresEmptyValues(): void {
		$this->information->addParameter('', 'Value');
		$dynamicProperties = $this->information->getDynamicProperties();

		self::assertEmpty($dynamicProperties);
	}

	public function testAddParameterIgnoresEmptyName(): void {
		$this->information->addParameter('Name', '');
		$dynamicProperties = $this->information->getDynamicProperties();

		self::assertEmpty($dynamicProperties);
	}

	public function testFluentInterface(): void {
		$result = $this->information
			->setOrder('PO-001')
			->setFromDate('2024-01-01')
			->setIssueDate('2024-03-15')
			->setDueDate('2024-03-30')
			->setTaxableSupplyDate('2024-03-15')
			->addParameter('Project', 'Test');

		self::assertSame($this->information, $result);
	}

	public function testGetPropertiesIncludesAllFields(): void {
		$this->information
			->setOrder('PO-001')
			->setIssueDate('2024-03-15')
			->addParameter('Project', 'Test');

		$properties = $this->information->getProperties();

		self::assertArrayHasKey('order', $properties);
		self::assertArrayHasKey('issueDate', $properties);
		self::assertArrayHasKey('parametr1', $properties);
	}
}
