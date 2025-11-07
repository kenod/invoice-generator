<?php

declare(strict_types=1);

namespace WFP\Invoice;

/**
 * Represents a single invoice item/line
 */
class InvoiceItem
{
    /**
     * Item name/description
     */
    public string $name;

    /**
     * Quantity
     */
    public float $quantity;

    /**
     * Unit of measurement (e.g., "pcs", "kg", "hours")
     */
    public string $unit;

    /**
     * Price per unit
     */
    public float $price;

    /**
     * VAT rate (e.g., 21 for 21%)
     */
    public float $vat;

    /**
     * Special item flag (used for internal processing)
     */
    public bool $special = false;

    /**
     * EAN barcode number
     */
    public ?int $ean = null;

    /**
     * Additional note for this item
     */
    public string $note = '';

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
        string $note = ''
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
}
