<?php declare(strict_types=1);

namespace Kenod\InvoiceGenerator;

use function imagecreatefromgif;
use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagesx;
use function imagesy;

/**
 * Invoice settings and configuration
 *
 * This class contains all configuration options for invoice generation including:
 * - Document metadata (number, author, title)
 * - VAT settings and rates
 * - Rounding options
 * - Display options
 * - Images and styling
 * - Electronic signature
 * - QR payment codes
 */
final class Settings {
	protected ?string $invoiceNumber = null;

	protected string $author = '';

	protected string $title = '';

	protected string $currency = 'CZK';

	protected string $filename = '';

	protected bool $vatPayer = false;

	protected float $itemSpacing = 0;

	/** @var bool|array{0?: int, 1?: int, 2?: int} */
	protected bool|array $underline = false;

	protected float $deposits = 0;

	/**
	 * Rounding precision (1=crowns, 2=fifties)
	 */
	protected int $roundTo = 1;

	/** @var list<array{0: string, 1: float, 2: string}> */
	protected array $signatureText = [];

	/** @var list<array{0: string, 1: float, 2: string}> */
	protected array $footerText = [];

	/** @var list<array{path: string, x: float, y: float|string, repeat: string, width: float|null, height: float|null}> */
	protected array $images = [];

	/**
	 * VAT rate labels [rate => label]
	 *
	 * @var array<int|string, string>
	 */
	protected array $vatRates = [
		'0' => 'Nulová sazba',
		'12' => 'Snížená sazba',
		'21' => 'Základní sazba',
	];

	protected int $roundingEnabled = 0;

	/**
	 * Rounding distribution (1=highest rate, 2=lowest rate, 3=highest total, 4=zero rate)
	 */
	protected int $roundingDistribution = 1;

	protected bool $roundingAsItem = true;

	protected bool $vatSummary = true;

	protected bool $alreadyPaidInPaymentInfo = false;

	/** @var array{mj: bool, pocetmj: bool, cenamj: bool} */
	protected array $displayedColumns = ['mj' => true, 'pocetmj' => true, 'cenamj' => true];

	/**
	 * Discount settings [amount, type, display_location, show_zero, vat_rate]
	 *
	 * @var array{0: float, 1: int, 2: int, 3: int, 4: float}
	 */
	protected array $discount = [0, 0, 1, 0, 0];

	protected bool $sendToMail = false;

	protected bool $summaryEmpty = true;

	protected string $signatureCertificate = '';

	protected string $signaturePassword = '';

	/**
	 * Rounding type (1=display only, 2=calculate too, 3=round total)
	 */
	protected int $roundingType = 1;

	/**
	 * Rounding method (1=mathematical, 2=up, 3=down)
	 */
	protected int $roundingMethod = 1;

	/**
	 * Document type (1=invoice, 2=proforma, 3=credit note, 4=storno)
	 */
	protected int $documentType = 1;

	/** @var array{Name: string, Location: string, Reason: string, ContactInfo: string} */
	protected array $signatureInfo = ['Name' => '', 'Location' => '', 'Reason' => '', 'ContactInfo' => ''];

	/**
	 * @var array{
	 *     fillColor: array{0: int, 1: int, 2: int},
	 *     fontColor: array{0: int, 1: int, 2: int},
	 *     itemFillColor: array{0: int, 1: int, 2: int}|false,
	 *     itemFontColor?: array{0: int, 1: int, 2: int},
	 *     pricesFillColor: array{0: int, 1: int, 2: int},
	 *     pricesFontColor: array{0: int, 1: int, 2: int},
	 *     signatureFillColor: array{0: int, 1: int, 2: int},
	 *     finalRecipientFillColor: array{0: int, 1: int, 2: int}
	 * }
	 */
	protected array $style = [
		'fillColor' => [225, 225, 225],
		'fontColor' => [0, 0, 0],
		'itemFillColor' => [240, 240, 240],
		'pricesFillColor' => [225, 225, 225],
		'pricesFontColor' => [0, 0, 0],
		'signatureFillColor' => [225, 225, 225],
		'finalRecipientFillColor' => [225, 225, 225],
	];

	/** @var array{display: bool, different_address: bool} */
	protected array $finalRecipient = ['display' => false, 'different_address' => false];

	/** @var array{display: bool, x: float, y: float|string, page: string, size: float, style: int} */
	protected array $qrPayment = ['display' => false, 'x' => 50, 'y' => 50, 'page' => 'F', 'size' => 20, 'style' => 1];

	/**
	 * Output type (I=inline, D=download, F=file, FI=file+inline, FD=file+download, E=email, S=string)
	 */
	protected string $outputType = 'I';

	/** @var array{code: int, top: float, left: float, width: float, height: float} */
	protected array $barcode = ['code' => 0, 'top' => 1, 'left' => 10, 'width' => 30, 'height' => 10];

	/** @var array{original_document?: string, reason?: string} */
	protected array $additionalInfo = [];

	protected bool $reverseCharge = false;

	protected string $reverseChargeText = '';

	protected string $font = 'dejavusans';

	protected bool $displayItemCount = true;

	protected bool $amountsWithVat = false;

	/** @var array<string, mixed> */
	protected array $eet = [];

	protected string $notePosition = 'bottom';

	/** @var array{enabled: bool, width?: float, color?: string, dash?: int} */
	protected array $borders = ['enabled' => false];

	protected string $documentName = '';

	protected bool $hideFooterTexts = false;

	/**
	 * Gets count of displayed columns
	 */
	public function getDisplayedColumnsCount(): int {
		$count = 0;

		foreach ($this->displayedColumns as $value) {
			if ($value) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Calculates total width of displayed columns
	 *
	 * @param bool $vatPayer Whether company is VAT payer
	 * @return int Total width in percent
	 */
	public function getDisplayedColumnsWidth(bool $vatPayer = true): int {
		$width = 0;

		if ($this->displayedColumns['mj']) {
			$width += $vatPayer ? 8 : 25;
		}

		if ($this->displayedColumns['pocetmj']) {
			$width += $vatPayer ? 17 : 15;
		}

		if ($this->displayedColumns['cenamj']) {
			$width += $vatPayer ? 10 : 25;
		}

		return $width;
	}

	public function setDocumentName(string $name): self {
		$this->documentName = $name;

		return $this;
	}

	public function setHideFooterTexts(bool $value): self {
		$this->hideFooterTexts = $value;

		return $this;
	}

	public function setAlreadyPaidInPaymentInfo(bool $value): self {
		$this->alreadyPaidInPaymentInfo = $value;

		return $this;
	}

	/**
	 * Resets settings for next invoice generation
	 *
	 * Only resets per-invoice settings, not global configuration like QR payment, font, etc.
	 */
	public function clear(): self {
		$this->invoiceNumber = null;
		$this->documentType = 1;
		$this->discount = [0, 0, 1, 0, 0];
		$this->deposits = 0;
		$this->eet = [];

		return $this;
	}

	public function setDisplayItemCount(bool $value): self {
		$this->displayItemCount = $value;

		return $this;
	}

	public function setNotePosition(string $position): self {
		$this->notePosition = $position;

		return $this;
	}

	/**
	 * Enables borders around invoice sections
	 *
	 * @param bool $draw Enable borders
	 * @param float $width Border width in mm
	 * @param string $color Border color (hex)
	 * @param int $dash Dash pattern (0=solid, >0=dashed)
	 */
	public function setBorders(bool $draw, float $width = 0.1, string $color = '000000', int $dash = 0): self {
		$this->borders['enabled'] = $draw;
		$this->borders['width'] = $width;
		$this->borders['color'] = $color;
		$this->borders['dash'] = $dash;

		return $this;
	}

	/**
	 * Gets TCPDF-compatible border configuration
	 *
	 * @return array<string, array{width: float, cap: string, join: string, dash: int, color: array{0: int, 1: int, 2: int}}>
	 */
	public function getBorders(): array {
		if (!$this->borders['enabled']) {
			return [];
		}

		$borderStyle = [
			'width' => $this->borders['width'] ?? 0.1,
			'cap' => 'butt',
			'join' => 'miter',
			'dash' => $this->borders['dash'] ?? 0,
			'color' => $this->getRGBColor($this->borders['color'] ?? '000000'),
		];

		return [
			'B' => $borderStyle,
			'T' => $borderStyle,
			'L' => $borderStyle,
			'R' => $borderStyle,
		];
	}

	/**
	 * Sets EET (Electronic Registration of Sales) data
	 *
	 * @param array<string, mixed> $data EET data
	 */
	public function setEET(array $data): self {
		$this->eet = $data;

		return $this;
	}

	public function setAmountsWithVat(bool $value): self {
		$this->amountsWithVat = $value;

		return $this;
	}

	/**
	 * Sets barcode parameters
	 *
	 * @param int $code Barcode value
	 * @param float $x X position in mm
	 * @param float $y Y position in mm
	 * @param float $width Width in mm
	 * @param float $height Height in mm
	 */
	public function setBarcode(int $code, float $x = 10, float $y = 1, float $width = 30, float $height = 10): self {
		$this->barcode['code'] = $code;
		$this->barcode['left'] = $x;
		$this->barcode['top'] = $y;
		$this->barcode['width'] = $width;
		$this->barcode['height'] = $height;

		return $this;
	}

	/**
	 * Sets additional information (for credit notes, storno)
	 *
	 * @param string $originalDocument Original document reference
	 * @param string $reason Reason for credit note/storno
	 */
	public function setAdditionalInfo(string $originalDocument, string $reason = ''): self {
		$this->additionalInfo['original_document'] = $originalDocument;

		if ($reason !== '') {
			$this->additionalInfo['reason'] = $reason;
		}

		return $this;
	}

	/**
	 * Enables reverse charge VAT mode
	 *
	 * @param bool $enabled Enable reverse charge
	 * @param string $text Optional custom text to display
	 */
	public function setReverseCharge(bool $enabled, string $text = ''): self {
		$this->reverseCharge = $enabled;
		$this->reverseChargeText = $text;

		return $this;
	}

	public function setFont(string $fontName): self {
		$this->font = $fontName;

		return $this;
	}

	/**
	 * Configures QR payment code display
	 *
	 * @param bool $display Show QR code
	 * @param float $x X position in mm
	 * @param float $y Y position in mm
	 * @param string $page Position (F=first, L=last, PU=payment section, PA=footer)
	 * @param float $size QR code size in mm
	 * @param int $style Style (1=framed+desc below, 2=no frame+desc right, 3=no frame+desc below)
	 */
	public function setQRPayment(
		bool $display,
		float $x = 50,
		float $y = 50,
		string $page = 'F',
		float $size = 20,
		int $style = 1,
	): self {
		$this->qrPayment['display'] = $display;
		$this->qrPayment['x'] = $x;
		$this->qrPayment['y'] = $y;
		$this->qrPayment['page'] = $page;
		$this->qrPayment['size'] = $size;
		$this->qrPayment['style'] = $style;

		return $this;
	}

	/**
	 * Sets style option
	 *
	 * @param string $style Style name (fillColor, fontColor, itemFillColor, etc.)
	 * @param mixed $value Color value (hex string or RGB array)
	 */
	public function setStyle(string $style, mixed $value): self {
		if ($style === 'itemFillColor' && $value === false) {
			$this->style[$style] = false;

			return $this;
		}

		if (!isset($this->style[$style])) {
			return $this;
		}

		$this->style[$style] = is_string($value) ? $this->getRGBColor($value) : $value;

		return $this;
	}

	public function setFinalRecipientDisplay(bool $value): self {
		$this->finalRecipient['display'] = $value;

		return $this;
	}

	public function setFinalRecipientDifferentAddress(bool $value): self {
		$this->finalRecipient['different_address'] = $value;

		return $this;
	}

	/**
	 * Sets invoice language
	 *
	 * @param string $language Language code (e.g., 'cs', 'sk', 'en')
	 */
	public function setLanguage(string $language): self {
		$language = str_replace(['.', '/', '\\'], '', $language);
		$langFile = dirname(__FILE__) . '/../langs/' . $language . '.php';

		if (is_file($langFile)) {
			Translator::loadTranslations($langFile);
		}

		return $this;
	}

	/**
	 * Sets document type
	 *
	 * @param string|int $type Document type (1/'faktura', 2/'proforma', 3/'odd', 4/'storno')
	 */
	public function setDocumentType(string|int $type): self {
		$typeMap = [
			'faktura' => 1,
			'proforma' => 2,
			'odd' => 3,
			'storno' => 4,
		];

		$this->documentType = is_string($type) ? $typeMap[$type] ?? 1 : $type;

		return $this;
	}

	/**
	 * Sets electronic signature certificate
	 *
	 * @param string $certificate Path to certificate file or certificate content
	 */
	public function setSignatureCertificate(string $certificate): self {
		$this->signatureCertificate = is_file($certificate) ? (string) file_get_contents($certificate) : $certificate;

		return $this;
	}

	public function setSignaturePassword(string $password): self {
		$this->signaturePassword = $password;

		return $this;
	}

	/**
	 * Sets signature information metadata
	 */
	public function setSignatureInfo(
		string $name = '',
		string $location = '',
		string $reason = '',
		string $contact = '',
	): self {
		$this->signatureInfo['Name'] = $name;
		$this->signatureInfo['Location'] = $location;
		$this->signatureInfo['Reason'] = $reason;
		$this->signatureInfo['ContactInfo'] = $contact;

		return $this;
	}

	/**
	 * Gets VAT rate label by rate value
	 *
	 * @param float $rate VAT rate (e.g., 21 for 21%)
	 * @return string|null Rate label or null if not found
	 */
	public function getVatRateLabel(float $rate): ?string {
		$rateKey = (string) $rate;

		return $this->vatRates[$rateKey] ?? null;
	}

	public function setSummaryEmpty(bool $value): self {
		$this->summaryEmpty = $value;

		return $this;
	}

	/**
	 * Sets discount parameters
	 *
	 * @param float $discount Discount amount or percentage
	 * @param int $type Discount type (0=amount, 1=percentage)
	 * @param int $displayLocation Display location (0=between items, 1=at total, 2=both)
	 * @param int $showZero Show when zero (0=only non-zero, 1=always)
	 * @param float $vatRate VAT rate to apply discount to
	 */
	public function setDiscount(
		float $discount,
		int $type = 0,
		int $displayLocation = 1,
		int $showZero = 0,
		float $vatRate = 0,
	): self {
		$this->discount = [$discount, $type, $displayLocation, $showZero, $vatRate];

		return $this;
	}

	/**
	 * Sets rounding options
	 *
	 * @param int $value Rounding precision (0=none, 1=crowns, 2=fifties)
	 * @param int $type Rounding type (1=display only, 2=calculate too, 3=round total)
	 * @param int $method Rounding method (1=mathematical, 2=up, 3=down)
	 * @param int $distribution Distribution (1=highest rate, 2=lowest rate, 3=highest total, 4=zero rate)
	 * @param bool $asItem Count rounding as invoice item
	 */
	public function setRounding(
		int $value,
		int $type = 1,
		int $method = 1,
		int $distribution = 1,
		bool $asItem = true,
	): self {
		$this->roundTo = $value;
		$this->roundingType = $type;
		$this->roundingMethod = $method;
		$this->roundingDistribution = $distribution;
		$this->roundingAsItem = $asItem;

		// Disable rounding if value is 0
		if ($value === 0) {
			$this->roundingEnabled = 2;
		}

		return $this;
	}

	/**
	 * Sets VAT rates with labels
	 *
	 * @param array<string, string> $rates VAT rates [rate => label]
	 */
	public function setVatRates(array $rates): self {
		$this->vatRates = $rates;

		return $this;
	}

	/**
	 * Ensures a VAT rate exists in the rates array
	 *
	 * If the rate doesn't exist, it will be added with a translated label
	 *
	 * @param float $rate VAT rate to ensure exists
	 */
	public function ensureVatRate(float $rate): void {
		$rateKey = (string) $rate;

		if (!isset($this->vatRates[$rateKey])) {
			$rateFormatted = str_replace('.', ',', $rateKey);
			$this->vatRates[$rateKey] = sprintf(Translator::t('vat_rate_label'), $rateFormatted);
		}
	}

	/**
	 * Sets which columns to display
	 *
	 * @param bool $unit Display unit column
	 * @param bool $quantity Display quantity column
	 * @param bool $unitPrice Display unit price column
	 */
	public function setDisplayedColumns(bool $unit, bool $quantity, bool $unitPrice): self {
		$this->displayedColumns['mj'] = $unit;
		$this->displayedColumns['pocetmj'] = $quantity;
		$this->displayedColumns['cenamj'] = $unitPrice;

		return $this;
	}

	public function setDisplayUnitColumn(bool $value): self {
		$this->displayedColumns['mj'] = $value;

		return $this;
	}

	public function setVatSummary(bool $value): self {
		$this->vatSummary = $value;

		return $this;
	}

	public function setSendEmail(bool $value): self {
		$this->sendToMail = $value;

		return $this;
	}

	/**
	 * Adds image to invoice
	 *
	 * @param string $path Path to image file
	 * @param float $horizontal Horizontal position in mm
	 * @param float|string $vertical Vertical position in mm or 'c-XX' for relative to signature
	 * @param string $repeat Repeat on pages (F=first, L=last, A=all)
	 * @param float|null $width Image width in mm (auto if null)
	 * @param float|null $height Image height in mm (auto if null)
	 */
	public function setImage(
		string $path,
		float $horizontal,
		float|string $vertical,
		string $repeat = 'F',
		?float $width = null,
		?float $height = null,
	): self {
		// Auto-calculate dimensions if not provided
		if ($width === null || $height === null) {
			$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			$imageResource = match ($extension) {
				'jpg', 'jpeg' => imagecreatefromjpeg($path),
				'gif' => imagecreatefromgif($path),
				'png' => imagecreatefrompng($path),
				default => false,
			};

			if ($imageResource !== false) {
				$pixelWidth = imagesx($imageResource);
				$pixelHeight = imagesy($imageResource);

				// Convert pixels to mm (assuming 96 DPI: pixels / 3.78 = mm)
				$width ??= $pixelWidth / 3.78;
				$height ??= $pixelHeight / 3.78;
			}
		}

		$this->images[] = [
			'path' => $path,
			'x' => $horizontal,
			'y' => $vertical,
			'repeat' => $repeat,
			'width' => $width,
			'height' => $height,
		];

		return $this;
	}

	/**
	 * Sets text next to signature line
	 *
	 * @param string $text Text to display
	 * @param float $fontSize Font size in pt
	 * @param string $style Font style (B=bold, I=italic, U=underline)
	 */
	public function setSignatureText(string $text, float $fontSize = 8, string $style = ''): self {
		$this->signatureText[] = [$text, $fontSize, $style];

		return $this;
	}

	/**
	 * Sets footer text at end of invoice
	 *
	 * @param string $text Text to display
	 * @param float $fontSize Font size in pt
	 * @param string $style Font style (B=bold, I=italic, U=underline)
	 */
	public function setFooterText(string $text, float $fontSize = 8, string $style = ''): self {
		$this->footerText[] = [$text, $fontSize, $style];

		return $this;
	}

	public function setInvoiceNumber(string $value): self {
		$this->invoiceNumber = $value;

		return $this;
	}

	public function setDeposits(float $amount): self {
		$this->deposits = $amount;

		return $this;
	}

	public function setItemSpacing(float $spacing): self {
		$this->itemSpacing = $spacing;

		return $this;
	}

	/**
	 * Sets item underline color
	 *
	 * @param array<string, mixed>|bool $value Underline configuration or false to disable
	 */
	public function setUnderline(array|bool $value): self {
		$this->underline = $value;

		return $this;
	}

	public function setVatPayer(bool $value): self {
		$this->vatPayer = $value;

		return $this;
	}

	public function setAuthor(string $author): self {
		$this->author = $author;

		return $this;
	}

	public function setTitle(string $title): self {
		$this->title = $title;

		return $this;
	}

	public function setCurrency(string $currency): self {
		$this->currency = $currency;

		return $this;
	}

	public function setFilename(string $filename): self {
		$this->filename = $filename;

		return $this;
	}

	/**
	 * Sets output type
	 *
	 * @param string $type Output type:
	 *   - I: Inline browser display
	 *   - D: Force download
	 *   - F: Save to file
	 *   - FI: Save to file + inline display
	 *   - FD: Save to file + force download
	 *   - E: Return as email attachment
	 *   - S: Return as string
	 */
	public function setOutputType(string $type): self {
		$this->outputType = $type;

		return $this;
	}

	public function getInvoiceNumber(): ?string {
		return $this->invoiceNumber;
	}

	public function getAuthor(): string {
		return $this->author;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getCurrency(): string {
		return $this->currency;
	}

	public function getFilename(): string {
		return $this->filename;
	}

	public function getVatPayer(): bool {
		return $this->vatPayer;
	}

	public function getItemSpacing(): float {
		return $this->itemSpacing;
	}

	/**
	 * @return bool|array{0?: int, 1?: int, 2?: int}
	 */
	public function getUnderline(): bool|array {
		return $this->underline;
	}

	public function getDeposits(): float {
		return $this->deposits;
	}

	public function getRoundTo(): int {
		return $this->roundTo;
	}

	/**
	 * @return list<array{0: string, 1: float, 2: string}>
	 */
	public function getSignatureText(): array {
		return $this->signatureText;
	}

	/**
	 * @return list<array{0: string, 1: float, 2: string}>
	 */
	public function getFooterText(): array {
		return $this->footerText;
	}

	/**
	 * @return list<array{path: string, x: float, y: float|string, repeat: string, width: float|null, height: float|null}>
	 */
	public function getImages(): array {
		return $this->images;
	}

	/**
	 * @return array<int|string, string>
	 */
	public function getVatRates(): array {
		return $this->vatRates;
	}

	public function getRoundingEnabled(): int {
		return $this->roundingEnabled;
	}

	public function getRoundingDistribution(): int {
		return $this->roundingDistribution;
	}

	public function getRoundingAsItem(): bool {
		return $this->roundingAsItem;
	}

	public function getVatSummary(): bool {
		return $this->vatSummary;
	}

	public function getAlreadyPaidInPaymentInfo(): bool {
		return $this->alreadyPaidInPaymentInfo;
	}

	/**
	 * @return array{mj: bool, pocetmj: bool, cenamj: bool}
	 */
	public function getDisplayedColumns(): array {
		return $this->displayedColumns;
	}

	/**
	 * @return array{0: float, 1: int, 2: int, 3: int, 4: float}
	 */
	public function getDiscount(): array {
		return $this->discount;
	}

	public function getSendToMail(): bool {
		return $this->sendToMail;
	}

	public function getSummaryEmpty(): bool {
		return $this->summaryEmpty;
	}

	public function getSignatureCertificate(): string {
		return $this->signatureCertificate;
	}

	public function getSignaturePassword(): string {
		return $this->signaturePassword;
	}

	public function getRoundingType(): int {
		return $this->roundingType;
	}

	public function getRoundingMethod(): int {
		return $this->roundingMethod;
	}

	public function getDocumentType(): int {
		return $this->documentType;
	}

	/**
	 * @return array{Name: string, Location: string, Reason: string, ContactInfo: string}
	 */
	public function getSignatureInfo(): array {
		return $this->signatureInfo;
	}

	/**
	 * @return array{fillColor: array{0: int, 1: int, 2: int}, fontColor: array{0: int, 1: int, 2: int}, itemFillColor: array{0: int, 1: int, 2: int}|false, itemFontColor?: array{0: int, 1: int, 2: int}, pricesFillColor: array{0: int, 1: int, 2: int}, pricesFontColor: array{0: int, 1: int, 2: int}, signatureFillColor: array{0: int, 1: int, 2: int}, finalRecipientFillColor: array{0: int, 1: int, 2: int}}
	 */
	public function getStyle(): array {
		return $this->style;
	}

	/**
	 * @return array{display: bool, different_address: bool}
	 */
	public function getFinalRecipient(): array {
		return $this->finalRecipient;
	}

	/**
	 * @return array{display: bool, x: float, y: float|string, page: string, size: float, style: int}
	 */
	public function getQrPayment(): array {
		return $this->qrPayment;
	}

	public function getOutputType(): string {
		return $this->outputType;
	}

	/**
	 * @return array{code: int, top: float, left: float, width: float, height: float}
	 */
	public function getBarcode(): array {
		return $this->barcode;
	}

	/**
	 * @return array{original_document?: string, reason?: string}
	 */
	public function getAdditionalInfo(): array {
		return $this->additionalInfo;
	}

	public function getReverseCharge(): bool {
		return $this->reverseCharge;
	}

	public function getReverseChargeText(): string {
		return $this->reverseChargeText;
	}

	public function getFont(): string {
		return $this->font;
	}

	public function getDisplayItemCount(): bool {
		return $this->displayItemCount;
	}

	public function getAmountsWithVat(): bool {
		return $this->amountsWithVat;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getEet(): array {
		return $this->eet;
	}

	public function getNotePosition(): string {
		return $this->notePosition;
	}

	/**
	 * @return array{enabled: bool, width?: float, color?: string, dash?: int}
	 */
	public function getBordersConfig(): array {
		return $this->borders;
	}

	public function getDocumentName(): string {
		return $this->documentName;
	}

	public function getHideFooterTexts(): bool {
		return $this->hideFooterTexts;
	}

	/**
	 * Converts HTML hex color to RGB array
	 *
	 * @param string $htmlColor Hex color (e.g., '#FF0000' or 'FF0000')
	 * @return array{0: int, 1: int, 2: int} RGB array
	 */
	private function getRGBColor(string $htmlColor): array {
		$htmlColor = ltrim($htmlColor, '#');

		return [
			(int) hexdec(substr($htmlColor, 0, 2)),
			(int) hexdec(substr($htmlColor, 2, 2)),
			(int) hexdec(substr($htmlColor, 4, 2)),
		];
	}
}
