<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator;

/**
 * Invoice information section (dates, order reference, etc.)
 */
final class Information {
	/**
	 * Order reference [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $order = ['Based on order no.:', null, false];

	/**
	 * Order date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $fromDate = ['From date:', null, false];

	/**
	 * Issue date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $issueDate = ['Issue date:', null, false];

	/**
	 * Due date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $dueDate = ['Due date:', null, false];

	/**
	 * Taxable supply date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $taxableSupplyDate = ['Taxable supply date:', null, true];

	/**
	 * Dynamic custom properties
	 *
	 * @var array<string, array{0: string, 1: string}>
	 */
	protected array $dynamicProperties = [];

	private int $parameterCount = 0;

	/**
	 * Gets order reference
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getOrder(): array {
		return $this->order;
	}

	/**
	 * Gets from date
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getFromDate(): array {
		return $this->fromDate;
	}

	/**
	 * Gets issue date
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getIssueDate(): array {
		return $this->issueDate;
	}

	/**
	 * Gets due date
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getDueDate(): array {
		return $this->dueDate;
	}

	/**
	 * Gets taxable supply date
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getTaxableSupplyDate(): array {
		return $this->taxableSupplyDate;
	}

	/**
	 * Gets dynamic properties
	 *
	 * @return array<string, array{0: string, 1: string}>
	 */
	public function getDynamicProperties(): array {
		return $this->dynamicProperties;
	}

	/**
	 * Translates basic information labels to current language
	 */
	public function translate(): self {
		$this->order[0] = Translator::t('based_on_order_no');
		$this->fromDate[0] = Translator::t('from_date');
		$this->issueDate[0] = Translator::t('issue_date');
		$this->dueDate[0] = Translator::t('due_date');
		$this->taxableSupplyDate[0] = Translator::t('tax_point_date');

		return $this;
	}

	/**
	 * Gets all static and dynamic properties
	 *
	 * @return array<string, mixed>
	 */
	public function getProperties(): array {
		$properties = [];

		foreach (get_object_vars($this) as $key => $value) {
			$properties[$key] = $value;
		}

		if ($this->dynamicProperties !== []) {
			foreach ($this->dynamicProperties as $key => $value) {
				$properties[$key] = $value;
			}
		}

		return $properties;
	}

	/**
	 * Adds custom parameter to information section
	 *
	 * @param string $name Parameter name/label
	 * @param string $value Parameter value
	 */
	public function addParameter(string $name, string $value): self {
		if ($name !== '' && $value !== '') {
			$this->parameterCount++;
			$key = 'parametr' . $this->parameterCount;

			if (!isset($this->dynamicProperties[$key])) {
				$this->dynamicProperties[$key] = [$name, $value];
			}
		}

		return $this;
	}

	/**
	 * Sets order reference number
	 *
	 * @param string $orderNumber Order reference
	 */
	public function setOrder(string $orderNumber): self {
		if ($orderNumber !== '') {
			$this->order[1] = $orderNumber;
			$this->order[2] = false;
		}

		return $this;
	}

	/**
	 * Sets order received date
	 *
	 * @param string $date Date in any format
	 */
	public function setFromDate(string $date): self {
		if ($date !== '') {
			$this->fromDate[1] = $date;
			$this->fromDate[2] = false;
		}

		return $this;
	}

	/**
	 * Sets invoice issue date
	 *
	 * @param string $date Date in any format
	 */
	public function setIssueDate(string $date): self {
		if ($date !== '') {
			$this->issueDate[1] = $date;
			$this->issueDate[2] = false;
		}

		return $this;
	}

	/**
	 * Sets invoice due date
	 *
	 * @param string $date Date in any format
	 */
	public function setDueDate(string $date): self {
		if ($date !== '') {
			$this->dueDate[1] = $date;
			$this->dueDate[2] = false;
		}

		return $this;
	}

	/**
	 * Sets taxable supply date
	 *
	 * @param string $date Date in any format
	 */
	public function setTaxableSupplyDate(string $date): self {
		if ($date !== '') {
			$this->taxableSupplyDate[1] = $date;
			$this->taxableSupplyDate[2] = true;
		}

		return $this;
	}
}
