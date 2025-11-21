<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator;

/**
 * Represents a single invoice item/line
 */
final class InvoiceItem {
	/**
	 * Item name/description
	 */
	protected string $name;

	/**
	 * Quantity
	 */
	protected float $quantity;

	/**
	 * Unit of measurement (e.g., "pcs", "kg", "hours")
	 */
	protected string $unit;

	/**
	 * Price per unit
	 */
	protected float $price;

	/**
	 * VAT rate (e.g., 21 for 21%)
	 */
	protected float $vat;

	/**
	 * Special item flag (used for internal processing)
	 */
	protected bool $special = false;

	/**
	 * EAN barcode number
	 */
	protected ?int $ean = null;

	/**
	 * Additional note for this item
	 */
	protected string $note = '';

	/**
	 * Creates a new invoice item
	 *
	 * @param string $name Item name/description
	 * @param float $quantity Quantity
	 * @param float $price Price per unit
	 * @param string $unit Unit of measurement
	 * @param float $vat VAT rate
	 * @param bool $special Special item flag
	 * @param int|null $ean EAN barcode
	 * @param string $note Additional note
	 */
	public function __construct(
		string $name,
		float $quantity,
		float $price,
		string $unit = '',
		float $vat = 0,
		bool $special = false,
		?int $ean = null,
		string $note = '',
	) {
		$this->name = $name;
		$this->quantity = $quantity;
		$this->price = $price;
		$this->unit = $unit;
		$this->vat = $vat;
		$this->special = $special;
		$this->ean = $ean;
		$this->note = $note;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getQuantity(): float {
		return $this->quantity;
	}

	public function getUnit(): string {
		return $this->unit;
	}

	public function getPrice(): float {
		return $this->price;
	}

	public function getVat(): float {
		return $this->vat;
	}

	public function getSpecial(): bool {
		return $this->special;
	}

	public function getEan(): ?int {
		return $this->ean;
	}

	public function getNote(): string {
		return $this->note;
	}

	public function setName(string $name): self {
		$this->name = $name;

		return $this;
	}

	public function setQuantity(float $quantity): self {
		$this->quantity = $quantity;

		return $this;
	}

	public function setUnit(string $unit): self {
		$this->unit = $unit;

		return $this;
	}

	public function setPrice(float $price): self {
		$this->price = $price;

		return $this;
	}

	public function setVat(float $vat): self {
		$this->vat = $vat;

		return $this;
	}

	public function setSpecial(bool $special): self {
		$this->special = $special;

		return $this;
	}

	public function setEan(?int $ean): self {
		$this->ean = $ean;

		return $this;
	}

	public function setNote(string $note): self {
		$this->note = $note;

		return $this;
	}
}
