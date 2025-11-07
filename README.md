# Danek Invoice Generator

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
- **Email Delivery**: Send invoices directly via email

## Requirements

- PHP 8.3 or 8.4
- TCPDF library (automatically installed via Composer)

## Installation

```bash
composer require danek/invoice-generator
```

## Quick Start

```php
<?php

use Danek\InvoiceGenerator\InvoiceGenerator;

// Create new invoice generator
$invoice = new InvoiceGenerator();

// Set supplier information
$invoice->supplier
    ->setCompany('My Company s.r.o.')
    ->setStreet('Main Street 123')
    ->setCity('Prague')
    ->setPostalCode('110 00')
    ->setCompanyId('12345678')
    ->setTaxId('CZ12345678');

// Set customer information
$invoice->customer
    ->setCompany('Customer Ltd.')
    ->setStreet('Customer Street 456')
    ->setCity('Brno')
    ->setPostalCode('602 00');

// Set invoice details
$invoice->information
    ->setInvoiceNumber('2024001')
    ->setIssueDate('2024-03-15')
    ->setDueDate('2024-03-30')
    ->setTaxableSupplyDate('2024-03-15');

// Set payment details
$invoice->paymentDetails
    ->setPaymentMethod('Bank transfer')
    ->setAccountNumber('123456789/0100')
    ->setVariableSymbol('2024001');

// Configure settings
$invoice->settings
    ->setVatPayer(true)
    ->setCurrency('CZK')
    ->setLanguage('cs');

// Add invoice items
$invoice->addItem('Web Development', 40, 1500, 'hours', 21);
$invoice->addItem('Hosting Services', 1, 500, 'month', 21);

// Generate and output PDF
$invoice->generate();
```

## Advanced Usage

### QR Payment Codes

```php
$invoice->settings->setQRPayment(
    display: true,
    x: 150,
    y: 200,
    page: 'F', // F=first page, L=last page
    size: 30,
    style: 1
);
```

### Electronic Signature

```php
$invoice->settings
    ->setSignatureCertificate('/path/to/certificate.pfx')
    ->setSignaturePassword('password')
    ->setSignatureInfo(
        name: 'John Doe',
        location: 'Prague',
        reason: 'Invoice approval',
        contact: 'john@example.com'
    );
```

### Custom Styling

```php
$invoice->settings
    ->setStyle('fillColor', [200, 220, 240])
    ->setStyle('fontColor', [0, 0, 100])
    ->setFont('helvetica');
```

### Multiple VAT Rates

```php
$invoice->settings->setVatRates([
    '0' => 'Zero rate',
    '12' => 'Reduced rate',
    '21' => 'Standard rate'
]);

$invoice->addItem('Books', 5, 200, 'pcs', 12); // 12% VAT
$invoice->addItem('Services', 1, 1000, 'hour', 21); // 21% VAT
```

### Discounts and Rounding

```php
// Set discount
$invoice->settings->setDiscount(
    discount: 10, // 10% or amount
    type: 1, // 0=amount, 1=percentage
    displayLocation: 1, // 0=between items, 1=at total, 2=both
    showZero: 0,
    vatRate: 21
);

// Set rounding
$invoice->settings->setRounding(
    value: 1, // 0=none, 1=crowns, 2=fifties
    type: 1, // 1=display only, 2=calculate too
    method: 1, // 1=mathematical, 2=up, 3=down
    distribution: 1, // 1=highest rate, 2=lowest, 3=highest total, 4=zero rate
    asItem: true
);
```

## Localization

The library includes Czech (cs) and Slovak (sk) translations. Add custom languages by creating files in `langs/` directory:

```php
// langs/en.php
return [
    'faktura' => 'Invoice',
    'dodavatel' => 'Supplier',
    'odberatel' => 'Customer',
    // ... more translations
];

// Use in code
$invoice->settings->setLanguage('en');
```

## Code Quality

This library follows strict coding standards:

```bash
# Run PHP CodeSniffer
composer phpcs

# Fix coding standard violations
composer phpcbf

# Run PHPStan static analysis
composer phpstan

# Run all checks
composer check
```

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Check code quality
composer check
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
use Danek\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();
$invoice->supplier->setCompany('Company');
$invoice->settings->setInvoiceNumber('001');
$invoice->addItem('Item', 1, 100);
$invoice->generate();
```

### Key Changes:
- Namespace: `WFPfaktury` → `Danek\InvoiceGenerator`
- Main class: `WFPfaktury` → `InvoiceGenerator`
- All method names now use camelCase: `SetX()` → `setX()`
- All property names translated to English
- PHP 8.3/8.4 required
- Deprecated methods removed

See [MIGRATION.md](MIGRATION.md) for complete details.

## License

Proprietary - See LICENSE file for details

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
