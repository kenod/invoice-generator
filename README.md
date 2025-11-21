# Invoice Generator

Professional PHP library for generating PDF invoices, proforma invoices, credit notes, and other accounting documents with QR payment codes, IBAN validation, and multi-language support.

> **Migrating from v2.x?** See the [Migration Guide](MIGRATION.md) for step-by-step instructions.

## Features

- **Multiple Document Types**: Invoice, Proforma, Credit Note, Storno
- **VAT Calculations**: Support for multiple VAT rates with automatic calculations
- **QR Payment Codes**: Generate Czech QR payment codes (SPD format)
- **IBAN Validation**: Validate and format Czech IBAN numbers
- **Electronic Signatures**: PDF digital signature support
- **Multi-language**: Easy localization with translation files
- **Professional Styling**: Customizable colors, fonts, and branding
- **Barcode Support**: 1D and 2D barcode generation
- **Type Safety**: Full PHP 8.3+ type hints and strict types
- **Unit Tested**: Comprehensive test coverage with PHPUnit

## Requirements

- PHP 8.3 or 8.4
- TCPDF library (automatically installed via Composer)
- GD extension (for image handling)

## Installation

```bash
composer require kenod/invoice-generator
```

## Quick Start

```php
<?php

use Kenod\InvoiceGenerator\InvoiceGenerator;

// Create new invoice generator
$invoice = new InvoiceGenerator();

// Set supplier information
$invoice->getSupplier()
    ->setCompany('My Company s.r.o.')
    ->setStreet('Main Street 123')
    ->setCity('Prague')
    ->setPostalCode('110 00')
    ->setCompanyId('12345678')
    ->setTaxId('CZ12345678');

// Set customer information
$invoice->getCustomer()
    ->setCompany('Customer Ltd.')
    ->setStreet('Customer Street 456')
    ->setCity('Brno')
    ->setPostalCode('602 00');

// Set invoice details
$invoice->getInformation()
    ->setIssueDate('2024-03-15')
    ->setDueDate('2024-03-30')
    ->setTaxableSupplyDate('2024-03-15');

// Set payment details
$invoice->getPaymentDetails()
    ->setPaymentMethod('Bank transfer')
    ->setAccountNumber('123456789/0100')
    ->setVariableSymbol('2024001');

// Configure settings
$invoice->getSettings()
    ->setInvoiceNumber('2024001')
    ->setVatPayer(true)
    ->setCurrency('CZK')
    ->setLanguage('cs');

// Add invoice items
$invoice->addItem('Web Development', 40, 1500, 'hours', 21);
$invoice->addItem('Hosting Services', 1, 500, 'month', 21);

// Generate and output PDF
$invoice->generate();
```

## Complete API Reference

### InvoiceGenerator Class

Main class for generating invoices.

#### Core Methods

```php
// Generate and output the PDF
$invoice->generate(bool $output = true): bool

// Add an invoice item
$invoice->addItem(
    string $name,           // Item name/description
    float $quantity,        // Quantity
    float $price,          // Unit price (without VAT if VAT payer)
    string $unit = 'pcs',  // Unit of measurement
    float $vat = 0,        // VAT rate (e.g., 21 for 21%)
    string $note = '',     // Additional note
    int|null $ean = null   // EAN barcode
): bool

// Get total price after generation
$invoice->getTotalPrice(): float

// Get final prices with VAT breakdown
$invoice->getFinalPrices(): array
// Returns: ['total' => float, 'vat_summary' => [...]]

// Clear all data for new invoice
$invoice->clearData(): void

// Output PDF directly (advanced usage)
$invoice->outputPDF(string $name = '', string $dest = ''): string
```

### Settings Class

Accessed via `$invoice->getSettings()`. All methods return `$this` for chaining.

#### Basic Settings

```php
// Set invoice number
->setInvoiceNumber(string $value): self

// Set document type
->setDocumentType(string|int $type): self
// Values: 1/'faktura', 2/'proforma', 3/'odd', 4/'storno'

// Set whether company is VAT payer
->setVatPayer(bool $value): self

// Set currency
->setCurrency(string $currency): self
// Default: 'CZK'

// Set language
->setLanguage(string $language): self
// Available: 'cs', 'sk', 'en'

// Set custom document name
->setDocumentName(string $name): self
```

#### PDF Metadata

```php
// Set PDF author
->setAuthor(string $author): self

// Set PDF title
->setTitle(string $title): self

// Set output filename
->setFilename(string $filename): self

// Set output type
->setOutputType(string $type): self
// Values: 'I' (inline), 'D' (download), 'F' (file),
//         'FI' (file+inline), 'FD' (file+download),
//         'E' (email), 'S' (string)
```

#### Financial Settings

```php
// Set discount
->setDiscount(
    float $discount,        // Discount amount or percentage
    int $type = 0,         // 0=amount, 1=percentage
    int $displayLocation = 1, // 0=between items, 1=at total, 2=both
    int $showZero = 0,     // 0=only non-zero, 1=always show
    float $vatRate = 0     // VAT rate to apply discount to
): self

// Set rounding
->setRounding(
    int $value,            // 0=none, 1=crowns, 2=fifties
    int $type = 1,        // 1=display only, 2=calculate too, 3=round total
    int $method = 1,      // 1=mathematical, 2=up, 3=down
    int $distribution = 1, // 1=highest rate, 2=lowest, 3=highest total, 4=zero rate
    bool $asItem = true   // Count rounding as invoice item
): self

// Set deposits/advances already paid
->setDeposits(float $amount): self

// Set whether amounts include VAT
->setAmountsWithVat(bool $value): self
```

#### VAT Settings

```php
// Set VAT rates with labels
->setVatRates(array $rates): self
// Example: ['0' => 'Zero rate', '21' => 'Standard rate']

// Get VAT rate label
->getVatRateLabel(float $rate): ?string

// Enable/disable VAT summary display
->setVatSummary(bool $value): self

// Show VAT summary even when empty
->setSummaryEmpty(bool $value): self

// Enable reverse charge VAT
->setReverseCharge(bool $enabled, string $text = ''): self
```

#### Display Options

```php
// Set which columns to display
->setDisplayedColumns(
    bool $unit,        // Display unit column
    bool $quantity,    // Display quantity column
    bool $unitPrice    // Display unit price column
): self

// Set unit column visibility
->setDisplayUnitColumn(bool $value): self

// Display item count in footer
->setDisplayItemCount(bool $value): self

// Set note position
->setNotePosition(string $position): self
// Values: 'top', 'bottom'

// Set spacing between items
->setItemSpacing(float $spacing): self

// Set item underline color
->setUnderline(array|bool $value): self
// Example: [255, 0, 0] for red or false to disable
```

#### Styling

```php
// Set style colors
->setStyle(string $style, mixed $value): self
// Styles: 'fillColor', 'fontColor', 'itemFillColor',
//         'itemFontColor', 'pricesFillColor', 'pricesFontColor',
//         'signatureFillColor', 'finalRecipientFillColor'
// Value: [R, G, B] array or hex string '#RRGGBB'

// Set font
->setFont(string $fontName): self
// Default: 'dejavusans', also available: 'helvetica', 'times', etc.

// Enable borders around sections
->setBorders(
    bool $draw,
    float $width = 0.1,
    string $color = '000000',
    int $dash = 0          // 0=solid, >0=dashed
): self
```

#### Images and Branding

```php
// Add image to invoice
->setImage(
    string $path,           // Path to image file
    float $horizontal,      // X position in mm
    float|string $vertical, // Y position in mm or 'c-XX' for relative
    string $repeat = 'F',   // F=first, L=last, A=all pages
    ?float $width = null,   // Width in mm (auto if null)
    ?float $height = null   // Height in mm (auto if null)
): self

// Add text next to signature
->setSignatureText(
    string $text,
    float $fontSize = 8,
    string $style = ''      // B=bold, I=italic, U=underline
): self

// Add footer text
->setFooterText(
    string $text,
    float $fontSize = 8,
    string $style = ''
): self
```

#### QR Payment Codes

```php
// Configure QR payment code
->setQRPayment(
    bool $display,          // Show QR code
    float $x = 50,         // X position in mm
    float $y = 50,         // Y position in mm
    string $page = 'F',    // F=first, L=last, PU=payment section, PA=footer
    float $size = 20,      // QR code size in mm
    int $style = 1         // 1=framed+desc below, 2=no frame+desc right, 3=no frame+desc below
): self
```

#### Barcodes

```php
// Add barcode to invoice
->setBarcode(
    int $code,             // Barcode value
    float $x = 10,        // X position in mm
    float $y = 1,         // Y position in mm
    float $width = 30,    // Width in mm
    float $height = 10    // Height in mm
): self
```

#### Electronic Signature

```php
// Set signature certificate
->setSignatureCertificate(string $certificate): self
// Can be file path or certificate content

// Set certificate password
->setSignaturePassword(string $password): self

// Set signature metadata
->setSignatureInfo(
    string $name = '',
    string $location = '',
    string $reason = '',
    string $contact = ''
): self
```

#### Final Recipient

```php
// Display final recipient section
->setFinalRecipientDisplay(bool $value): self

// Use different address for final recipient
->setFinalRecipientDifferentAddress(bool $value): self
```

#### Additional Information

```php
// Set additional info (for credit notes/storno)
->setAdditionalInfo(
    string $originalDocument,
    string $reason = ''
): self

// Set EET (Electronic Registration of Sales) data
->setEET(array $data): self

// Show "Already paid" in payment info
->setAlreadyPaidInPaymentInfo(bool $value): self
```

#### Utility Methods

```php
// Get count of displayed columns
->getDisplayedColumnsCount(): int

// Get total width of displayed columns
->getDisplayedColumnsWidth(bool $vatPayer = true): int

// Clear settings for new invoice
->clear(): self
```

### Address Class

Used for supplier, customer, and final recipient. Accessed via `$invoice->getSupplier()`, `$invoice->getCustomer()`, `$invoice->getEndRecipient()`.

```php
// Set company name
->setCompany(string $company): self

// Set contact person name
->setName(string $name): self

// Set street address
->setStreet(string $street): self

// Set city
->setCity(string $city): self

// Set postal code
->setPostalCode(string $postalCode): self

// Set country
->setCountry(string $country): self

// Set company ID (IČO)
->setCompanyId(string $companyId): self

// Set tax ID (DIČ)
->setTaxId(string $taxId): self

// Set phone number
->setPhone(string $phone): self

// Set email
->setEmail(string $email): self

// Set website
->setWeb(string $web): self

// Translate labels to current language
->translate(): self
```

### Information Class

Invoice information. Accessed via `$invoice->getInformation()`.

```php
// Set order number
->setOrder(string $order): self

// Set date from
->setFromDate(string $date): self

// Set issue date
->setIssueDate(string $date): self

// Set due date
->setDueDate(string $date): self

// Set taxable supply date (date of performance)
->setTaxableSupplyDate(string $date): self

// Add custom parameter
->addParameter(string $label, string $value): self

// Translate labels to current language
->translate(): self
```

### PaymentDetails Class

Payment details. Accessed via `$invoice->getPaymentDetails()`.

```php
// Set payment method
->setPaymentMethod(string $method): self

// Set account number
->setAccountNumber(string $accountNumber): self

// Set bank code
->setBankCode(string $bankCode): self

// Set variable symbol
->setVariableSymbol(string $variableSymbol): self

// Set constant symbol
->setConstantSymbol(string $constantSymbol): self

// Set specific symbol
->setSpecificSymbol(string $specificSymbol): self

// Add custom parameter
->addParameter(string $label, string $value): self

// Translate labels to current language
->translate(): self
```

### Iban Class

Static utility class for IBAN operations.

```php
use Kenod\InvoiceGenerator\Iban;

// Generate IBAN from Czech account number
Iban::getIban(
    string|int $account,
    string|int $bankCode,
    string|int $accountPrefix = '',
    bool $onlyValidBic = false
): string|false

// Get BIC/SWIFT code for Czech bank
Iban::getSwift(string|int $bankCode): string

// Generate QR payment string
Iban::getQRString(array $data): string|null
// Data: ['iban' => '', 'bic' => '', 'amount' => '', 'vs' => '', 'ks' => '', 'ss' => '']
```

### Translator Class

Static utility class for translations.

```php
use Kenod\InvoiceGenerator\Translator;

// Load translation file
Translator::loadTranslations(string $filePath): void

// Translate key
Translator::t(string $key): string

// Check if language is loaded
Translator::hasLanguage(): bool
```

## Advanced Usage Examples

### Multiple Document Types

```php
// Proforma invoice
$invoice->settings->setDocumentType('proforma');

// Credit note
$invoice->settings->setDocumentType('odd');

// Storno/Cancellation
$invoice->settings->setDocumentType('storno');
$invoice->settings->setAdditionalInfo('2024001', 'Cancelled order');
```

### Complex Invoice with All Features

```php
$invoice = new InvoiceGenerator();

// Supplier
$invoice->supplier
    ->setCompany('Tech Solutions s.r.o.')
    ->setStreet('Innovation Street 123')
    ->setCity('Prague')
    ->setPostalCode('110 00')
    ->setCountry('Czech Republic')
    ->setCompanyId('12345678')
    ->setTaxId('CZ12345678')
    ->setPhone('+420 123 456 789')
    ->setEmail('info@techsolutions.cz')
    ->setWeb('www.techsolutions.cz');

// Customer
$invoice->customer
    ->setCompany('Customer Corp.')
    ->setName('John Doe')
    ->setStreet('Business Avenue 456')
    ->setCity('Brno')
    ->setPostalCode('602 00')
    ->setCompanyId('87654321')
    ->setTaxId('CZ87654321');

// Invoice information
$invoice->information
    ->setOrder('PO-2024-001')
    ->setFromDate('2024-01-01')
    ->setIssueDate('2024-03-15')
    ->setDueDate('2024-03-30')
    ->setTaxableSupplyDate('2024-03-15')
    ->addParameter('Project', 'Website Development');

// Payment details
$invoice->paymentDetails
    ->setPaymentMethod('Bank transfer')
    ->setAccountNumber('123456789')
    ->setBankCode('0100')
    ->setVariableSymbol('2024001')
    ->setConstantSymbol('0308')
    ->setSpecificSymbol('12345');

// Settings
$invoice->settings
    ->setInvoiceNumber('2024001')
    ->setDocumentType('faktura')
    ->setVatPayer(true)
    ->setCurrency('CZK')
    ->setLanguage('cs')
    ->setAuthor('John Doe')
    ->setTitle('Invoice 2024001')
    ->setFont('dejavusans')
    ->setDiscount(10, 1, 1, 0, 21)
    ->setRounding(1, 1, 1, 1, true)
    ->setDeposits(5000)
    ->setVatRates(['0' => 'Nulová sazba', '12' => 'Snížená sazba', '21' => 'Základní sazba'])
    ->setDisplayedColumns(true, true, true)
    ->setDisplayItemCount(true)
    ->setVatSummary(true)
    ->setStyle('fillColor', [200, 220, 240])
    ->setStyle('fontColor', [0, 0, 100])
    ->setQRPayment(true, 150, 250, 'F', 30, 1)
    ->setImage('logo.png', 15, 10, 'A', 40, 20)
    ->setSignatureText('Authorized by: John Doe', 8, 'B')
    ->setFooterText('Thank you for your business!', 8, 'I')
    ->setBorders(true, 0.2, '000000', 0);

// Add items with different VAT rates
$invoice->addItem('Web Development', 40, 1500, 'hours', 21);
$invoice->addItem('Consulting', 10, 2000, 'hours', 21);
$invoice->addItem('Books', 5, 200, 'pcs', 12);
$invoice->addItem('Training Materials', 1, 500, 'set', 0);

// Generate
$invoice->generate();
```

### Reverse Charge Invoice

```php
$invoice->settings
    ->setVatPayer(true)
    ->setReverseCharge(true, 'Reverse charge - Article 196 of Directive 2006/112/EC');

$invoice->addItem('International Services', 100, 1500, 'hours', 21);
// VAT will be 0, but rate is preserved for documentation
```

### Getting Price Information

```php
// Get final prices with VAT breakdown
$prices = $invoice->getFinalPrices();

echo "Total: " . $prices['total'] . " CZK\n";

if (isset($prices['vat_summary'])) {
    foreach ($prices['vat_summary'] as $rate => $amounts) {
        echo "VAT {$rate}%: Base=" . $amounts['base']
           . ", VAT=" . $amounts['vat']
           . ", Total=" . $amounts['total'] . "\n";
    }
}
```

## Testing

The library includes comprehensive unit tests with PHPUnit.

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
vendor/bin/phpunit --coverage-html coverage

# Run specific test class
vendor/bin/phpunit tests/InvoiceGeneratorTest.php

# Run with testdox output
vendor/bin/phpunit --testdox
```

### Test Coverage

The library includes 93 tests with 280 assertions covering:

- **InvoiceGenerator**: Basic invoice generation, price calculations, VAT handling, discounts, rounding, deposits, reverse charge
- **Settings**: Configuration options, fluent interface, type safety, all setter/getter methods
- **Address**: All address fields and translation
- **Information**: Date handling and custom parameters
- **PaymentDetails**: Payment method configuration
- **InvoiceItem**: All item properties and setters
- **Translator**: Translation loading, key lookup, fallbacks
- **Comprehensive**: All features working together, multi-document generation, clearData()
- **Price Calculations**: Non-VAT payer, VAT payer, multiple rates, discounts, deposits, rounding

All tests use strict type checking and follow PHPUnit 11 best practices.

## Code Quality

This library follows strict coding standards and includes automated quality checks:

```bash
# Run PHP CodeSniffer (PSR-12 + Slevomat standards)
composer phpcs

# Auto-fix coding standard violations
composer phpcbf

# Run PHPStan static analysis (Level 8)
composer phpstan

# Run all quality checks
composer check
```

### Quality Tools

- **PHPStan Level 8**: Strictest static analysis
- **PHP CodeSniffer**: PSR-12 + Slevomat Coding Standard
- **PHPUnit 11**: Latest testing framework
- **Strict Types**: `declare(strict_types=1)` on all files

## Localization

The library includes Czech (cs), Slovak (sk), and English (en) translations.

### Available Translation Keys

All translation keys use English snake_case format:

```
generated_by, generated_footer, page, invoice, invoice_header_taxpayer,
invoice_header_non_taxpayer, proforma, proforma_header, credit_note,
credit_note_header, corrective_document_header, cancellation,
cancellation_header_taxpayer, cancellation_header, supplier, customer,
end_recipient, payment_details, already_paid_do_not_pay, items_to_pay,
item_name, unit, quantity, unit_price, vat_rate, without_vat, vat,
with_vat, price_total, total_to_pay, payment_method, account_number,
bank_code, variable_symbol, constant_symbol, specific_symbol,
based_on_order_no, from_date, issue_date, due_date, tax_point_date,
company_id, tax_id, summary, vat_base, total_without_vat, total_vat,
total_with_vat, signature, discount, rounding, reverse_charge_note,
corrective_document_for_invoice, credit_note_for_invoice,
cancellation_for_invoice, eet_registration, business, register,
register_date, mode, fik_code, bkp_code, pkp_code
```

### Creating Custom Translations

```php
// Create langs/de.php
return [
    'invoice' => 'Rechnung',
    'supplier' => 'Lieferant',
    'customer' => 'Kunde',
    'total_to_pay' => 'Gesamtsumme',
    // ... more translations
];

// Use in code
$invoice->settings->setLanguage('de');
```

## Migration from Version 2.x

If you're upgrading from the old WFPfaktury class, please read the **[complete Migration Guide](MIGRATION.md)** which includes:

- Complete property name mapping
- Complete method name mapping
- Step-by-step migration instructions
- Side-by-side code comparisons
- List of removed deprecated methods

**Quick example:**

**Old code (v2.x):**
```php
$pdf = new \WFPfaktury\WFPfaktury();
$pdf->dodavatel->SetFirma('Company');
$pdf->nastaveni->SetCisloFaktury('001');
$pdf->pridejPolozku('Item', 1, 100);
$pdf->generuj();
```

**New code (v3.0):**
```php
use Kenod\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();
$invoice->supplier->setCompany('Company');
$invoice->settings->setInvoiceNumber('001');
$invoice->addItem('Item', 1, 100);
$invoice->generate();
```

### Key Changes:
- Namespace: `WFPfaktury` → `Kenod\InvoiceGenerator`
- Main class: `WFPfaktury` → `InvoiceGenerator`
- All method names now use camelCase: `SetX()` → `setX()`
- All property names translated to English
- PHP 8.3/8.4 required
- Deprecated methods removed
- Email functionality removed (use external library)

See [MIGRATION.md](MIGRATION.md) for complete details.

## Examples

See the `examples/` directory for complete working examples:

- `01_basic_invoice.php` - Simple invoice with minimal setup
- `02_eet.php` - Invoice with Electronic Registration of Sales (EET)
- `03_reverse_charge.php` - Reverse charge invoice
- `04_translations.php` - Using different languages
- `05_eco_print.php` - Eco-friendly compact layout
- `06_images.php` - Adding logos and images
- `07_custom_callback.php` - Custom PDF modifications
- `08_english.php` - English language invoice
- `09_multiple_documents.php` - Multiple documents in one PDF
- `10_credit_note.php` - Credit note with all features
- `11_comprehensive.php` - Comprehensive test with all available features

See the [examples README](examples/README.md) for detailed descriptions of each example.

## Performance Tips

1. **Reuse instance**: Create one `InvoiceGenerator` instance and use `clearData()` between invoices
2. **Disable features**: Turn off unused features (QR codes, barcodes, signatures) for faster generation
3. **Optimize images**: Use compressed images with appropriate dimensions
4. **Font subsetting**: Enabled by default, reduces PDF size
5. **Batch processing**: Generate multiple invoices in one script execution

## Troubleshooting

### Common Issues

**PDF not generating:**
- Check that TCPDF is installed: `composer show tecnickcom/tcpdf`
- Verify PHP version: `php -v` (must be 8.3+)
- Check error logs for TCPDF errors

**QR codes not showing:**
- Verify account number format (with bank code)
- Check that amount is > 0
- Ensure QR payment is enabled: `setQRPayment(true, ...)`

**Images not displaying:**
- Check file path is absolute or correct relative path
- Verify GD extension is installed: `php -m | grep -i gd`
- Supported formats: PNG, JPG, GIF

**Wrong language:**
- Ensure language file exists in `langs/` directory
- Call `setLanguage()` before any translation occurs
- Check translation key names (use English snake_case)

## License

This project is licensed under the **MIT License**.

You are free to use, modify, and distribute this library in your commercial and non-commercial projects.

See the [LICENSE](LICENSE) file for full details.

## Author

- **Petr Daněk** - Original author
- Email: danek@chci-www.cz
- Website: https://www.danekpetr.cz

## Credits

- Built on [TCPDF](https://tcpdf.org/) library
- Refactored for PHP 8.3+ compatibility
- Follows [Slevomat Coding Standard](https://github.com/slevomat/coding-standard)

## Support

For issues and feature requests, please use the GitHub issue tracker.

## Version

Current version: **3.0.0**

Changelog:
- 3.0.0: Complete refactoring for PHP 8.3+, English API, type safety, unit tests
- 2.x: Legacy WFPfaktury implementation
