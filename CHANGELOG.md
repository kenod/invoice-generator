# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2025-11-21

### Added
- **PHP 8.3/8.4 Support**: Complete rewrite for modern PHP with strict types
- **Encapsulation**: All properties changed from public to protected with getter/setter methods
- **Fluent Interface**: All setters return `$this` for method chaining
- **Type Safety**: Full type hints on all methods and properties
- **Unit Tests**: Comprehensive test suite with 93 tests and 280 assertions
- **Code Quality**: PHPStan Level 8, PSR-12, Slevomat Coding Standard
- **English API**: All property and method names translated to English
- **Getters**: Added getter methods for all classes (Address, Settings, Information, PaymentDetails, InvoiceItem)
- **New Methods**:
  - `getFinalPrices()` - Get price breakdown with VAT summary
  - `getSupplier()`, `getCustomer()`, `getEndRecipient()` - Access address objects
  - `getSettings()`, `getInformation()`, `getPaymentDetails()` - Access configuration objects
  - `setCustomCallback()` - Set custom PDF callback function
- **New Features**:
  - `setAdditionalInfo()` - For credit notes and corrections
  - `setBarcode()` - Add barcodes to invoices
  - `setVatRates()` - Custom VAT rate labels
  - `setItemSpacing()` - Control spacing between items
  - `setSignatureText()` - Multiple signature text lines
  - `setFooterText()` - Multiple footer text lines
  - `setBorders()` - Configurable section borders
  - `setUnderline()` - Configurable underlines
- **New Document Types**: Credit note (3), improved storno support
- **New Examples**:
  - `08_english.php` - English language invoice
  - `09_multiple_documents.php` - Multiple documents in one PDF
  - `10_credit_note.php` - Credit note demonstration
  - `11_comprehensive.php` - All features demonstration
- **Documentation**: Complete API reference, migration guide, examples README

### Changed
- **Namespace**: `WFPfaktury\WFPfaktury` → `Kenod\InvoiceGenerator`
- **Main Class**: `WFPfaktury` → `InvoiceGenerator`
- **Method Names**: All methods converted from PascalCase to camelCase
  - `SetFirma()` → `setCompany()`
  - `SetCisloFaktury()` → `setInvoiceNumber()`
  - `PridejPolozku()` → `addItem()`
  - etc. (see MIGRATION.md for complete mapping)
- **Property Names**: All properties translated to English
  - `$dodavatel` → `$supplier`
  - `$odberatel` → `$customer`
  - `$nastaveni` → `$settings`
  - etc.
- **Property Access**: Changed from public to protected, use getters instead
  - `$invoice->supplier->setCompany()` → `$invoice->getSupplier()->setCompany()`
  - `$invoice->settings->setInvoiceNumber()` → `$invoice->getSettings()->setInvoiceNumber()`
- **InvoiceItem Constructor**: Updated parameter order (name, quantity, price, unit, vat, special, ean, note)
- **Information Getters**: Return arrays with [label, value, display] instead of plain strings
- **PaymentDetails Getters**: Return arrays with [label, value, display] instead of plain strings
- **Translation Keys**: All keys now use English snake_case format
- **PHP Version**: Minimum version raised to 8.3

### Removed
- **Email Functionality**: Removed built-in email sending (use PHPMailer or similar)
- **Deprecated Methods**: Removed all legacy and deprecated methods
- **Public Properties**: All public properties now protected, must use getters
- **PHP 7.x Support**: No longer compatible with PHP 7.x
- **Old Namespace**: `WFPfaktury\WFPfaktury` namespace removed

### Fixed
- **Type Errors**: All type inconsistencies resolved
- **Null Handling**: Proper null handling with nullable types
- **Array Types**: Proper array type declarations
- **Price Calculations**: Improved accuracy with proper rounding
- **VAT Summary**: Correct VAT breakdown for multiple rates
- **Discount Application**: Fixed discount calculation with multiple VAT rates
- **Rounding Distribution**: Proper rounding distribution to VAT rates

### Security
- **Input Validation**: Added validation for all user inputs
- **Type Safety**: Strict types prevent type juggling vulnerabilities
- **Path Sanitization**: Image and file paths properly sanitized

### Migration
- See [MIGRATION.md](MIGRATION.md) for complete migration guide from v2.x to v3.0
- Side-by-side code comparisons provided
- Complete property and method name mapping
- Step-by-step instructions

### Testing
- **93 tests** with **280 assertions**
- PHPUnit 11 compatibility
- 100% method coverage for main classes
- Continuous integration ready

### Documentation
- Complete README.md with API reference
- MIGRATION.md with detailed upgrade guide
- Examples README with all example descriptions
- Inline PHPDoc comments for all methods
- Type hints for IDE autocompletion

## [2.x] - Legacy Version

Previous implementation as WFPfaktury class.
- Czech-only API
- Public properties
- PHP 7.x compatible
- Limited type safety
- No unit tests

### Note on Versioning

Version 3.0.0 represents a complete rewrite and is not backwards compatible with 2.x.
If you need to maintain compatibility with the old API, please continue using version 2.x.

[3.0.0]: https://github.com/kenod/invoice-generator/releases/tag/v3.0.0
