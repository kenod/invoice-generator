# Migration Guide: WFPfaktury v2.x → Danek\InvoiceGenerator v3.0

This guide will help you migrate from the old `WFPfaktury` class to the new refactored `Danek\InvoiceGenerator` library.

## Table of Contents

- [Breaking Changes](#breaking-changes)
- [Namespace Changes](#namespace-changes)
- [Class Name Changes](#class-name-changes)
- [Property Name Changes](#property-name-changes)
- [Method Name Changes](#method-name-changes)
- [Step-by-Step Migration](#step-by-step-migration)
- [Complete Example Comparison](#complete-example-comparison)

## Breaking Changes

### Major Changes

1. **Namespace**: Changed from `WFPfaktury` to `Danek\InvoiceGenerator`
2. **Main Class**: `WFPfaktury` → `InvoiceGenerator`
3. **Autoloading**: Now uses PSR-4 autoloading via Composer
4. **PHP Version**: Requires PHP 8.3 or 8.4
5. **Type Hints**: All methods now use strict typing
6. **Deprecated Methods**: All deprecated methods have been removed

## Namespace Changes

### Old (v2.x)
```php
require 'WFPfaktury.php';
$pdf = new \WFPfaktury\WFPfaktury();
```

### New (v3.0)
```php
require 'vendor/autoload.php';
use Danek\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();
```

## Class Name Changes

| Old Class (v2.x) | New Class (v3.0) |
|-----------------|-----------------|
| `WFPfaktury` | `InvoiceGenerator` |
| `WFPf_adresa` | `Address` |
| `WFPf_informace` | `Information` |
| `WFPf_platebniUdaje` | `PaymentDetails` |
| `WFPf_polozka` | `InvoiceItem` |
| `WFPf_email` | `Email` |
| `WFPf_nastaveni` | `Settings` |
| `WFPf_preklad` | `Translation` |
| `WFPf_iban` | `Iban` |

## Property Name Changes

### Main Class Properties

| Old Property (v2.x) | New Property (v3.0) |
|-------------------|-------------------|
| `$dodavatel` | `$supplier` |
| `$odberatel` | `$customer` |
| `$konecnyPrijemce` | `$endRecipient` |
| `$informace` | `$information` |
| `$platebniUdaje` | `$paymentDetails` |
| `$nastaveni` | `$settings` |

### Address Class Properties

| Old Property (v2.x) | New Property (v3.0) |
|-------------------|-------------------|
| `$firma` | `$company` |
| `$jmeno` | `$name` |
| `$ulice` | `$street` |
| `$psc` | `$postalCode` |
| `$mesto` | `$city` |
| `$zeme` | `$country` |
| `$ic` | `$companyId` |
| `$dic` | `$taxId` |
| `$telefon` | `$phone` |

### Settings Class Properties

| Old Property (v2.x) | New Property (v3.0) |
|-------------------|-------------------|
| `$cislo_faktury` | `$invoiceNumber` |
| `$platceDPH` | `$vatPayer` |
| `$mena` | `$currency` |
| `$autor` | `$author` |
| `$titulek` | `$title` |
| `$jmenosouboru` | `$filename` |
| `$vzdalenost_polozek` | `$itemSpacing` |
| `$podtrzeni` | `$underline` |
| `$zalohy` | `$deposits` |
| `$sazbyDPH` | `$vatRates` |
| `$shrnutiDPH` | `$vatSummary` |

### InvoiceItem Properties

| Old Property (v2.x) | New Property (v3.0) |
|-------------------|-------------------|
| `$nazev` | `$name` |
| `$mnozstvi` | `$quantity` |
| `$mj` | `$unit` |
| `$cena` | `$price` |
| `$dph` | `$vat` |
| `$poznamka` | `$note` |

## Method Name Changes

### Main Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `generuj()` | `generate()` |
| `pridejPolozku()` | `addItem()` |
| `celkovaCena()` | `getTotalPrice()` |
| `vratKonecneCeny()` | `getFinalPrices()` |

### Address Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `SetFirma()` | `setCompany()` |
| `SetJmeno()` | `setName()` |
| `SetUlice()` | `setStreet()` |
| `SetPSC()` | `setPostalCode()` |
| `SetMesto()` | `setCity()` |
| `SetZeme()` | `setCountry()` |
| `SetIC()` | `setCompanyId()` |
| `SetDIC()` | `setTaxId()` |
| `SetTelefon()` | `setPhone()` |
| `SetEmail()` | `setEmail()` |
| `SetWeb()` | `setWeb()` |
| `preklad()` | `translate()` |

### Information Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `SetObjednavka()` | `setOrder()` |
| `SetZedne()` | `setFromDate()` |
| `SetVystaveni()` | `setIssueDate()` |
| `SetSplatnost()` | `setDueDate()` |
| `SetPlneni()` | `setTaxableSupplyDate()` |
| `AddParametr()` | `addParameter()` |
| `preklad()` | `translate()` |

### PaymentDetails Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `SetZpusobuhrady()` | `setPaymentMethod()` |
| `SetCislouctu()` | `setAccountNumber()` |
| `SetKodbanky()` | `setBankCode()` |
| `SetVS()` | `setVariableSymbol()` |
| `SetKS()` | `setConstantSymbol()` |
| `SetSS()` | `setSpecificSymbol()` |
| `AddParametr()` | `addParameter()` |
| `preklad()` | `translate()` |

### Settings Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `SetCisloFaktury()` | `setInvoiceNumber()` |
| `SetPlatceDPH()` | `setVatPayer()` |
| `SetMena()` | `setCurrency()` |
| `SetAutor()` | `setAuthor()` |
| `SetTitulek()` | `setTitle()` |
| `SetNazevSouboru()` | `setFilename()` |
| `SetVzdalenostPolozek()` | `setItemSpacing()` |
| `SetPodtrzeni()` | `setUnderline()` |
| `SetUhrazenoZalohou()` | `setDeposits()` |
| `SetSazbyDPH()` | `setVatRates()` |
| `SetShrnutiDPH()` | `setVatSummary()` |
| `SetZaokrouhleni()` | `setRounding()` |
| `SetSleva()` | `setDiscount()` |
| `SetObrazek()` | `setImage()` |
| `SetTextUPodpisu()` | `setSignatureText()` |
| `SetTextKonec()` | `setFooterText()` |
| `SetJazyk()` | `setLanguage()` |
| `SetTypDokladu()` | `setDocumentType()` |
| `SetPodpis()` | `setSignatureCertificate()` |
| `SetPodpisHeslo()` | `setSignaturePassword()` |
| `SetPodpisInfo()` | `setSignatureInfo()` |
| `SetQRPlatba()` | `setQRPayment()` |
| `SetCarovyKod()` | `setBarcode()` |
| `SetReverseCharge()` | `setReverseCharge()` |
| `SetFont()` | `setFont()` |
| `SetStyle()` | `setStyle()` |
| `SetKonecnyPrijemceVypisovat()` | `setFinalRecipientDisplay()` |
| `SetKonecnyPrijemceOdlisnaAdresa()` | `setFinalRecipientDifferentAddress()` |
| `SetZobrazeneSloupce()` | `setDisplayedColumns()` |
| `SetVypisovatPocetPolozek()` | `setDisplayItemCount()` |
| `setPozicePoznamky()` | `setNotePosition()` |
| `setBorders()` | `setBorders()` *(same name)* |
| `setEET()` | `setEET()` *(same name)* |
| `setCastkySDPH()` | `setAmountsWithVat()` |
| `SetDoplnujiciInformace()` | `setAdditionalInfo()` |
| `GetSazba()` | `getVatRateLabel()` |
| `setNazevDokladu()` | `setDocumentName()` |
| `setJizUhrazenoVPlatebnichUdajich()` | `setAlreadyPaidInPaymentInfo()` |

### Email Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `SetAddress()` | `setAddress()` |
| `SetBody()` | `setBody()` |
| `SetSubject()` | `setSubject()` |
| `SetFrom()` | `setFrom()` |
| `SetFromName()` | `setFromName()` |
| `SetPhpMailerPath()` | `setPhpMailerPath()` |

### Translation Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `WFPf_preklad::t()` | `Translation::t()` |
| `WFPf_preklad::jeJazyk()` | `Translation::hasLanguage()` |

### IBAN Class Methods

| Old Method (v2.x) | New Method (v3.0) |
|------------------|------------------|
| `WFPf_iban::getIban()` | `Iban::getIban()` |
| `WFPf_iban::getSwift()` | `Iban::getSwift()` |
| `WFPf_iban::getQRString()` | `Iban::getQRString()` |

## Step-by-Step Migration

### Step 1: Install via Composer

```bash
composer require danek/invoice-generator
```

### Step 2: Update Your Require Statement

**Old:**
```php
require 'WFPfaktury.php';
```

**New:**
```php
require 'vendor/autoload.php';
```

### Step 3: Update Namespace and Class Instantiation

**Old:**
```php
$pdf = new \WFPfaktury\WFPfaktury();
```

**New:**
```php
use Danek\InvoiceGenerator\InvoiceGenerator;
$invoice = new InvoiceGenerator();
```

### Step 4: Update Property Names

**Old:**
```php
$pdf->dodavatel->SetFirma('My Company');
$pdf->odberatel->SetFirma('Customer');
$pdf->nastaveni->SetCisloFaktury('001');
```

**New:**
```php
$invoice->supplier->setCompany('My Company');
$invoice->customer->setCompany('Customer');
$invoice->settings->setInvoiceNumber('001');
```

### Step 5: Update Method Calls

**Old:**
```php
$pdf->pridejPolozku('Item', 1, 100, 'pcs', 21);
$pdf->generuj();
```

**New:**
```php
$invoice->addItem('Item', 1, 100, 'pcs', 21);
$invoice->generate();
```

## Complete Example Comparison

### Old Code (v2.x)

```php
<?php
require 'WFPfaktury.php';

$pdf = new \WFPfaktury\WFPfaktury();

// Supplier
$pdf->dodavatel->SetJmeno('Petr Daněk')
    ->SetUlice('Dolní Bečva 330')
    ->SetPSC('756 55')
    ->SetMesto('Dolní Bečva')
    ->SetIC('123213')
    ->SetDIC('CZ8604142141');

// Customer
$pdf->odberatel->SetFirma('Kostra Jiří')
    ->SetUlice('Dolní Bečva 147')
    ->SetPSC('756 55')
    ->SetMesto('Dolní Bečva');

// Settings
$pdf->nastaveni->SetJazyk('cs')
    ->SetPlatceDPH(true)
    ->SetCisloFaktury('15/2014')
    ->SetMena('CZK');

// Information
$pdf->informace->SetVystaveni('22.12.2014')
    ->SetSplatnost('05.01.2015');

// Payment details
$pdf->platebniUdaje->SetZpusobuhrady('Převodem')
    ->SetCislouctu('197220727/0800')
    ->SetVS('2014001');

// Items
$pdf->pridejPolozku('Web Development', 1, 1500, 'hours', 21);
$pdf->pridejPolozku('Hosting', 1, 500, 'month', 21);

// Generate
$pdf->generuj();
```

### New Code (v3.0)

```php
<?php
require 'vendor/autoload.php';

use Danek\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();

// Supplier
$invoice->supplier
    ->setName('Petr Daněk')
    ->setStreet('Dolní Bečva 330')
    ->setPostalCode('756 55')
    ->setCity('Dolní Bečva')
    ->setCompanyId('123213')
    ->setTaxId('CZ8604142141');

// Customer
$invoice->customer
    ->setCompany('Kostra Jiří')
    ->setStreet('Dolní Bečva 147')
    ->setPostalCode('756 55')
    ->setCity('Dolní Bečva');

// Settings
$invoice->settings
    ->setLanguage('cs')
    ->setVatPayer(true)
    ->setInvoiceNumber('15/2014')
    ->setCurrency('CZK');

// Information
$invoice->information
    ->setIssueDate('22.12.2014')
    ->setDueDate('05.01.2015');

// Payment details
$invoice->paymentDetails
    ->setPaymentMethod('Převodem')
    ->setAccountNumber('197220727/0800')
    ->setVariableSymbol('2014001');

// Items
$invoice->addItem('Web Development', 1, 1500, 'hours', 21);
$invoice->addItem('Hosting', 1, 500, 'month', 21);

// Generate
$invoice->generate();
```

## Removed Deprecated Methods

The following deprecated methods have been removed in v3.0:

- `SetCislo_faktury()` → use `setInvoiceNumber()`
- `SetUhrazeno_zalohou()` → use `setDeposits()`
- `SetVzdalenost_polozek()` → use `setItemSpacing()`
- `SetText_u_podpisu()` → use `setSignatureText()`
- `SetText_konec()` → use `setFooterText()`
- `SetJmenosouboru()` → use `setFilename()`
- `SetSend_email()` → use `setSendEmail()`
- `SetFrom_name()` → use `setFromName()`
- `SetPhp_mailer_path()` → use `setPhpMailerPath()`
- `SetFillcolor()` → use `setStyle()`
- `SetZaokrouhlovatDPH()` → removed (functionality changed)

## Need Help?

If you encounter issues during migration:

1. Check the [README.md](README.md) for updated examples
2. Look at [examples/example_modern.php](examples/example_modern.php) for a complete working example
3. Open an issue on GitHub

## Benefits of Migration

- **Type Safety**: Full type hints prevent bugs
- **Better IDE Support**: Autocomplete works perfectly
- **Modern PHP**: Uses PHP 8.3+ features
- **Cleaner Code**: English names are more intuitive
- **Maintainable**: PSR-4 autoloading and proper structure
- **Quality**: Follows Slevomat Coding Standard
- **Tested**: PHPStan level 8 compliance
