<?php declare(strict_types=1);

/**
 * Example 9: Multiple Documents in One PDF
 *
 * This example demonstrates how to generate multiple documents in a single PDF file.
 * It shows how to use clearData() to reset and generate multiple invoices/documents.
 * Includes invoice with EET, second invoice, and a delivery note.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();

// Supplier information (dodavatel)
$invoice->getSupplier()
	->setName('Tomáš Suchý')
	->setStreet('Dolní Bečva 123')
	->setPostalCode('756 55')
	->setCity('Dolní Bečva')
	->setCompanyId('123213')
	->setTaxId('CZ8604142141');

// Invoice settings (nastavení)
$invoice->getSettings()
	->setDisplayItemCount(false)
	->setLanguage('cs')
	->setFont('FreeSans')
	->setFinalRecipientDisplay(false)
	->setFinalRecipientDifferentAddress(true)
	->setVatPayer(true)
	->setVatSummary(true)
	->setAuthor('Tomáš Suchý')
	->setTitle('Faktura')
	->setCurrency('CZK')
	->setUnderline(['color' => '000000'])
	->setReverseCharge(false)
	->setRounding(1, 1, 1, 4, false) // Rounding to crowns, distribute to zero rate
	->setInvoiceNumber('15/2014')
	->setSignatureText('Podnikatel je zapsán do živnostenského rejstříku.', 8)
	->setSummaryEmpty(false)
	->setDisplayedColumns(false, true, true)
	->setQRPayment(true, 20, 3, 'PA', 20, 3)
	->setNotePosition('top')
	->setDiscount(12.5, 0, 2, 0, 21)
	->setBorders(true, 0.3, '000000')
	->setDeposits(35_872_021)
	->setAlreadyPaidInPaymentInfo(true);

// Styling - white theme
$invoice->getSettings()
	->setStyle('fillColor', 'ffffff')
	->setStyle('fontColor', '000000')
	->setStyle('pricesFillColor', 'ffffff')
	->setStyle('pricesFontColor', '000000')
	->setStyle('itemFillColor', 'ffffff')
	->setStyle('itemFontColor', '000000');

// Customer information (odběratel)
$invoice->getCustomer()
	->setCompany('Kostra Jiří')
	->setStreet('Dolní Bečva 147')
	->setPostalCode('756 55')
	->setCity('Dolní Bečva')
	->setCompanyId('515454')
	->setTaxId('CZ54754241');

// Invoice dates (informace)
$issueDate = strtotime('2014-12-22');
$invoice->getInformation()
	->setIssueDate(date('d.m.Y', $issueDate))
	->setDueDate(date('d.m.Y', $issueDate + 3600 * 24 * 14))
	->setTaxableSupplyDate(date('d.m.Y', $issueDate))
	->addParameter('Interní označení:', 'GP040');

// Payment details (platební údaje)
$invoice->getPaymentDetails()
	->setPaymentMethod('Převodem')
	->addParameter('IBAN:', 'CZ65 0800 0000 1920 0014 5399')
	->addParameter('SWIFT:', 'KOMB CZ PP')
	->setAccountNumber('197220727 / 0800')
	->setVariableSymbol('');

// Add invoice items (položky)
$invoice->addItem('Malířské a natěračské práce na hotelu Tesla', 1, 454.55, '', 21, '');
$invoice->addItem('Programování v PHP', 984.54, 30_054.7, 'Hod.', 21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$invoice->addItem('Programování v PHP', 50, 300, 'Hod.', 21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$invoice->addItem('Programování v PHP', 50, 300, 'Hod.', 21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$invoice->addItem('Programování v PHP', 50, 300, 'Hod.', 21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$invoice->addItem('Malířské a natěračské práce ve valašském muzeu', 1, 10_800, '', 21, '');

// Get final prices (useful for EET integration)
$prices = $invoice->getFinalPrices();

// EET (Electronic Registration of Sales)
$invoice->getSettings()->setEET([
	'fik' => 'fac367af-019c-4fc8-a747-dbff21b07235-ff',
	'pkp' => 'G/x3I4cOcy5nYnui+4TMrktpKY55+h2sUe2SO7QB92TW8krvWuIVyuiE8qONeBnFrHwWaN3kzP5HWn6zGJHnaxp0SFr7KTyNaYSAr4h6Ef/lZ8bBTdPYo+Lq8CZW/q7Q91mAGwN+CFyyOWlGJr8lsBt8cO6zsbs2Dsu7jK5AlW9c0zaYgtnYf24JckeiBe1veUVkDZkt7IFn9QNV22b/nKm3r/yONN1dnGOcQdIIw3PYz49hrNgPTD+6MBPKEpv7hYJeh00ICMAa1LmYNXmIL+MoIESxBhWkI3HCBXmnNX+Q+rsgcpKsfueuZ4x5obYYcu78v/Q33X/DIF7XK7WknQ==',
	'bkp' => '28a385b8-56a1191b-9d208736-41f49e94-a6dca14f',
	'rezim' => 'Běžný',
	'pokladna' => '11',
	'provozovna' => '22141',
	'datum' => time(),
	'Vlastní hodnota' => 'CZ12345678',
]);

// Generate first invoice (don't output yet)
$invoice->generate(false);

// ============================================
// SECOND INVOICE
// ============================================

$invoice->clearData();

$invoice->getSettings()->setInvoiceNumber('16/2018');

$invoice->getCustomer()
	->setCompany('Šmudla Jan')
	->setStreet('Bayerova 147')
	->setPostalCode('756 61')
	->setCity('Rožnov pod Radhoštěm')
	->setCompanyId('123456')
	->setTaxId('CZ7485742569');

$issueDate2 = strtotime('2018-09-12');
$invoice->getInformation()
	->setIssueDate(date('d.m.Y', $issueDate2))
	->setDueDate(date('d.m.Y', $issueDate2 + 3600 * 24 * 14))
	->setTaxableSupplyDate(date('d.m.Y', $issueDate2))
	->addParameter('Interní označení:', 'TEST02');

$invoice->getPaymentDetails()
	->setPaymentMethod('Převodem')
	->addParameter('IBAN:', 'CZ65 0800 0000 1920 0014 5399')
	->addParameter('SWIFT:', 'KOMB CZ PP')
	->setAccountNumber('197220727 / 0800');

$invoice->addItem('Vývoj WP pluginu', 1, 4454.55, '', 21, '');
$invoice->addItem('Programování v PHP', 50, 300, 'Hod.', 21, 'Test poznámky');

// Generate second invoice
$invoice->generate(false);

// ============================================
// THIRD DOCUMENT - DELIVERY NOTE
// ============================================

$invoice->clearData();

$invoice->getSettings()
	->setDocumentName('DODACÍ LIST k faktuře č. 16/2018')
	->setVatPayer(false)
	->setDisplayedColumns(false, true, true)
	->setInvoiceNumber('17/2018');

$invoice->getCustomer()
	->setCompany('Šmudla Jan')
	->setStreet('Bayerova 147')
	->setPostalCode('756 61')
	->setCity('Rožnov pod Radhoštěm');

$issueDate3 = strtotime('2018-09-12');
$invoice->getInformation()
	->setIssueDate(date('d.m.Y', $issueDate3))
	->setDueDate(date('d.m.Y', $issueDate3 + 3600 * 24 * 14))
	->setTaxableSupplyDate(date('d.m.Y', $issueDate3));

$invoice->getPaymentDetails()->setPaymentMethod('Hotově');

$invoice->addItem('Vývoj WP pluginu', 1, 1000, '', 21, '');

// Generate third document
$invoice->generate(false);

// ============================================
// OUTPUT CONFIGURATION
// ============================================

$invoice->getSettings()
	->setFilename(__DIR__ . '/09_multiple_documents.pdf')
	->setOutputType('FI') // Save to file and display inline
	->setSendEmail(false);

$invoice->outputPDF();
