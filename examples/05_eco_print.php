<?php declare(strict_types=1);

/**
 * Example 5: Economical Print (Ekonomický tisk)
 *
 * This example demonstrates how to generate an invoice with minimal styling
 * for economical printing (white background, black borders).
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();

// Supplier (with extended info)
$invoice->getSupplier()
	->setCompany('Web From Pixels group')
	->setName('Petr Daněk')
	->setStreet('Dolní Bečva 330')
	->setPostalCode('756 55')
	->setCity('Dolní Bečva')
	->setCompanyId('123213')
	->setTaxId('CZ8604142141')
	->setWeb('http://www.kenod.net')
	->setEmail('kenod@kenod.net')
	->setPhone('+420 732 253 134');

// Settings
$invoice->getSettings()
	->setDisplayItemCount(false)
	->setLanguage('cs')
	->setFont('FreeSans')
	->setFinalRecipientDisplay(false)
	->setFinalRecipientDifferentAddress(true)
	->setVatPayer(true)
	->setVatSummary(true)
	->setAuthor('Petr Daněk')
	->setTitle('Faktura')
	->setCurrency('CZK')
	->setReverseCharge(false)
	->setRounding(0, 1, 1, 4, false) // No rounding
	->setInvoiceNumber('15/2014')
	->setSignatureText('Podnikatel je zapsán do živnostenského rejstříku.', 8)
	->setSummaryEmpty(false)
	->setDisplayUnitColumn(true) // Show unit column
	->setQRPayment(true, 20, 3, 'PA', 20, 3)
	->setNotePosition('bottom')
	->setUnderline(['color' => '000000'])
	->setBorders(true, 0.3, '000000');

// Styling - white theme (economical)
$invoice->getSettings()
	->setStyle('fillColor', 'ffffff')
	->setStyle('fontColor', '000000')
	->setStyle('pricesFillColor', 'ffffff')
	->setStyle('pricesFontColor', '000000')
	->setStyle('itemFillColor', 'ffffff')
	->setStyle('itemFontColor', '000000');

// Customer
$invoice->getCustomer()
	->setCompany('Kostra Jiří')
	->setStreet('Dolní Bečva 147')
	->setPostalCode('756 55')
	->setCity('Dolní Bečva')
	->setCompanyId('515454')
	->setTaxId('CZ54754241');

// Invoice dates
$issueDate = strtotime('2014-12-22');
$invoice->getInformation()
	->setIssueDate(date('d.m.Y', $issueDate))
	->setDueDate(date('d.m.Y', $issueDate + 3600 * 24 * 14))
	->setTaxableSupplyDate(date('d.m.Y', $issueDate))
	->addParameter('Interní označení:', 'GP040');

// Payment details
$invoice->getPaymentDetails()
	->setPaymentMethod('Převodem')
	->addParameter('IBAN:', 'CZ65 0800 0000 1920 0014 5399')
	->addParameter('SWIFT:', 'KOMB CZ PP')
	->setAccountNumber('197220727 / 0800')
	->setVariableSymbol('');

// Add items
$invoice->addItem('Malířské a natěračské práce na hotelu Tesla', 1, 454, '', 21, '');
$invoice->addItem('Programování v PHP', 50, 300, 'Hod.', 21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$invoice->addItem('Malířské a natěračské práce ve valašském muzeu', 1, 10_800, '', 21, '');

// Get final prices
$prices = $invoice->getFinalPrices();

// Output configuration
$invoice->getSettings()
	->setFilename(__DIR__ . '/05_eco_print.pdf')
	->setOutputType('FI');

// Generate invoice
$invoice->generate();
