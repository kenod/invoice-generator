<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator;

/**
 * Payment details section of invoice
 */
final class PaymentDetails {
	/**
	 * Payment method [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $paymentMethod = ['Payment method:', null, false];

	/**
	 * Account number [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $accountNumber = ['Account number:', null, false];

	/**
	 * Bank code [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $bankCode = ['Bank code:', null, false];

	/**
	 * Variable symbol [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $variableSymbol = ['Variable symbol:', null, false];

	/**
	 * Constant symbol [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $constantSymbol = ['Constant symbol:', null, false];

	/**
	 * Specific symbol [label, value, visibility]
	 *
	 * @var array{0: string, 1: string|null, 2: bool}
	 */
	protected array $specificSymbol = ['Specific symbol:', null, false];

	/**
	 * Dynamic custom properties
	 *
	 * @var array<string, array{0: string, 1: string}>
	 */
	protected array $dynamicProperties = [];

	private int $parameterCount = 0;

	/**
	 * Gets payment method
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getPaymentMethod(): array {
		return $this->paymentMethod;
	}

	/**
	 * Gets account number
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getAccountNumber(): array {
		return $this->accountNumber;
	}

	/**
	 * Gets bank code
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getBankCode(): array {
		return $this->bankCode;
	}

	/**
	 * Gets variable symbol
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getVariableSymbol(): array {
		return $this->variableSymbol;
	}

	/**
	 * Gets constant symbol
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getConstantSymbol(): array {
		return $this->constantSymbol;
	}

	/**
	 * Gets specific symbol
	 *
	 * @return array{0: string, 1: string|null, 2: bool}
	 */
	public function getSpecificSymbol(): array {
		return $this->specificSymbol;
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
	 * Translates payment details labels to current language
	 */
	public function translate(): self {
		$this->paymentMethod[0] = Translator::t('payment_method');
		$this->accountNumber[0] = Translator::t('account_number');
		$this->bankCode[0] = Translator::t('bank_code');
		$this->variableSymbol[0] = Translator::t('variable_symbol');
		$this->constantSymbol[0] = Translator::t('constant_symbol');
		$this->specificSymbol[0] = Translator::t('specific_symbol');

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
	 * Adds custom parameter to payment details section
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
	 * Sets payment method (transfer, cash, etc.)
	 *
	 * @param string $paymentMethod Payment method
	 */
	public function setPaymentMethod(string $paymentMethod): self {
		if ($paymentMethod !== '') {
			$this->paymentMethod[1] = $paymentMethod;
			$this->paymentMethod[2] = false;
		}

		return $this;
	}

	/**
	 * Sets constant symbol
	 *
	 * @param string $constantSymbol Constant symbol
	 */
	public function setConstantSymbol(string $constantSymbol): self {
		if ($constantSymbol !== '') {
			$this->constantSymbol[1] = $constantSymbol;
			$this->constantSymbol[2] = false;
		}

		return $this;
	}

	/**
	 * Sets specific symbol
	 *
	 * @param string $specificSymbol Specific symbol
	 */
	public function setSpecificSymbol(string $specificSymbol): self {
		if ($specificSymbol !== '') {
			$this->specificSymbol[1] = $specificSymbol;
			$this->specificSymbol[2] = false;
		}

		return $this;
	}

	/**
	 * Sets account number (without bank code)
	 *
	 * @param string $accountNumber Account number
	 */
	public function setAccountNumber(string $accountNumber): self {
		if ($accountNumber !== '') {
			$this->accountNumber[1] = $accountNumber;
			$this->accountNumber[2] = false;
		}

		return $this;
	}

	/**
	 * Sets bank code
	 *
	 * @param string $bankCode Bank code
	 */
	public function setBankCode(string $bankCode): self {
		if ($bankCode !== '') {
			$this->bankCode[1] = $bankCode;
			$this->bankCode[2] = false;
		}

		return $this;
	}

	/**
	 * Sets variable symbol
	 *
	 * @param string $variableSymbol Variable symbol
	 */
	public function setVariableSymbol(string $variableSymbol): self {
		if ($variableSymbol !== '') {
			$this->variableSymbol[1] = $variableSymbol;
			$this->variableSymbol[2] = false;
		}

		return $this;
	}
}
