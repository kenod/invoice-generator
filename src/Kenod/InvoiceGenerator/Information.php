<?php

declare(strict_types=1);

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
	public array $order = ['Based on order no.:', null, false];

	/**
	 * Order date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	public array $fromDate = ['From date:', null, false];

	/**
	 * Issue date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	public array $issueDate = ['Issue date:', null, false];

	/**
	 * Due date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	public array $dueDate = ['Due date:', null, false];

	/**
	 * Taxable supply date [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	public array $taxableSupplyDate = ['Taxable supply date:', null, true];

	/**
	 * Dynamic custom properties
	 *
	 * @var array<string, array{0: string, 1: string}>
	 */
	public array $dynamicProperties = [];

	private int $parameterCount = 0;

	/**
	 * Translates basic information labels to current language
	 */
	public function translate(): self {
		$this->order[0] = Translation::t('na_zaklade_objednavky_c');
		$this->fromDate[0] = Translation::t('ze_dne');
		$this->issueDate[0] = Translation::t('datum_vystaveni');
		$this->dueDate[0] = Translation::t('datum_splatnosti');
		$this->taxableSupplyDate[0] = Translation::t('datum_zdanitelneho_plneni');

		return $this;
	}

	/**
	 * Gets all static and dynamic properties
	 *
	 * @return array<string, mixed>
	 */
	public function getProperties(): array {
		$properties = [];

		foreach ($this as $key => $value) {
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
		}

		return $this;
	}
}
