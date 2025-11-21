<?php declare(strict_types=1);

/**
 * Example 7: Custom Callback
 *
 * This example demonstrates how to use a custom callback function
 * to add custom TCPDF operations to the invoice.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();

// Supplier
$invoice->getSupplier()
	->setName('Petr Daněk')
	->setStreet('Dolní Bečva 330')
	->setPostalCode('756 55')
	->setCity('Dolní Bečva')
	->setCompanyId('123213')
	->setTaxId('CZ8604142141');

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
	->setRounding(1, 1, 1, 4, false)
	->setInvoiceNumber('15/2014')
	->setSignatureText('Podnikatel je zapsán do živnostenského rejstříku.', 8)
	->setSummaryEmpty(false)
	->setDisplayUnitColumn(false)
	->setQRPayment(true, 20, 3, 'PA', 20, 3)
	->setNotePosition('top');

// Styling - gray theme
$invoice->getSettings()
	->setStyle('fillColor', '999999')
	->setStyle('fontColor', 'ffffff')
	->setStyle('pricesFillColor', '999999')
	->setStyle('pricesFontColor', 'ffffff')
	->setStyle('itemFillColor', 'dddddd')
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
$invoice->addItem('Malířské a natěračské práce na hotelu Tesla', 1, 454.55, '', 21, '');
$invoice->addItem('Programování v PHP', 50, 300, 'Hod.', 21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$invoice->addItem('Malířské a natěračské práce ve valašském muzeu', 1, 10_800, '', 21, '');

/**
 * Custom callback function
 *
 * This function will be called during PDF generation and allows you to
 * add custom TCPDF operations. You have full access to all TCPDF methods.
 *
 * @param InvoiceGenerator $pdf The invoice generator instance (extends TCPDF)
 */
function myCustomCallback(InvoiceGenerator $pdf): void {
	// Add custom text centered at position 10, 220
	$pdf->SetFont('dejavusans', 'B', 20);
	$pdf->SetXY(10, 220);
	$pdf->Cell(190, 8, 'Custom callback text', 0, 0, 'C');

	// You can add any TCPDF operations here:
	// - Add text, images, shapes
	// - Draw lines, rectangles
	// - Add watermarks
	// - etc.
}

// Set the custom callback
$invoice->setCustomCallback('myCustomCallback');

// Output configuration
$invoice->getSettings()
	->setFilename(__DIR__ . '/07_custom_callback.pdf')
	->setOutputType('FI');

// Generate invoice
$invoice->generate();
