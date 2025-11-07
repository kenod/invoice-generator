<?php

/**
 * Modern Invoice Generator Example
 *
 * This example demonstrates the new API for generating invoices
 * using the refactored Kenod\InvoiceGenerator library.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

// Create new invoice generator instance
$invoice = new InvoiceGenerator();

// Configure supplier (dodavatel) information
$invoice->supplier
    ->setName('Petr Daněk')
    ->setStreet('Dolní Bečva 330')
    ->setPostalCode('756 55')
    ->setCity('Dolní Bečva')
    ->setCompanyId('123213')
    ->setTaxId('CZ8604142141');

// Configure customer (odběratel) information
$invoice->customer
    ->setCompany('Kostra Jiří')
    ->setStreet('Dolní Bečva 147')
    ->setPostalCode('756 55')
    ->setCity('Dolní Bečva')
    ->setCompanyId('515454')
    ->setTaxId('CZ54754241');

// Configure invoice information
$issueDate = strtotime('2024-12-22');
$invoice->information
    ->setIssueDate(date('d.m.Y', $issueDate))
    ->setDueDate(date('d.m.Y', $issueDate + 3600 * 24 * 14))
    ->setTaxableSupplyDate(date('d.m.Y', $issueDate))
    ->addParameter('Interní označení:', 'GP040');

// Configure payment details
$invoice->paymentDetails
    ->setPaymentMethod('Převodem')
    ->addParameter('IBAN:', 'CZ65 0800 0000 1920 0014 5399')
    ->addParameter('SWIFT:', 'KOMB CZ PP')
    ->setAccountNumber('197220727 / 0800')
    ->setVariableSymbol('2024001');

// Configure invoice settings
$invoice->settings
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
    ->setUnderline(['color' => '000000'])
    ->setReverseCharge(false)
    ->setRounding(1, 1, 1, 4, false) // Rounding to crowns, distribute to zero rate
    ->setInvoiceNumber('15/2024')
    ->setSignatureText('Podnikatel je zapsán do živnostenského rejstříku.', 8)
    ->setSummaryEmpty(false)
    ->setDisplayedColumns(false, true, true)
    ->setQRPayment(true, 20, 3, 'PA', 20, 3)
    ->setNotePosition('top')
    ->setDiscount(12.5, 0, 2, 0, 21)
    ->setBorders(true, 0.3, '000000')
    ->setDeposits(35_872_021)
    ->setAlreadyPaidInPaymentInfo(true);

// Configure styling (white theme)
$invoice->settings
    ->setStyle('fillColor', 'ffffff')
    ->setStyle('fontColor', '000000')
    ->setStyle('pricesFillColor', 'ffffff')
    ->setStyle('pricesFontColor', '000000')
    ->setStyle('itemFillColor', 'ffffff')
    ->setStyle('itemFontColor', '000000');

// Add invoice items
$invoice->addItem(
    'Malířské a natěračské práce na hotelu Tesla',
    1,
    454.55,
    '',
    21,
    ''
);

$invoice->addItem(
    'Programování v PHP',
    984.54,
    30_054.7,
    'Hod.',
    21,
    '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script'
);

$invoice->addItem(
    'Programování v PHP',
    50,
    300,
    'Hod.',
    21,
    '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script'
);

$invoice->addItem(
    'Malířské a natěračské práce ve valašském muzeu',
    1,
    10_800,
    '',
    21,
    ''
);

// Configure EET (Electronic Registration of Sales)
$invoice->settings->setEET([
    'fik' => 'fac367af-019c-4fc8-a747-dbff21b07235-ff',
    'pkp' => 'G/x3I4cOcy5nYnui+4TMrktpKY55+h2sUe2SO7QB92TW8krvWuIVyuiE8qONeBnFrHwWaN3kzP5HWn6zGJHnaxp0SFr7KTyNaYSAr4h6Ef/lZ8bBTdPYo+Lq8CZW/q7Q91mAGwN+CFyyOWlGJr8lsBt8cO6zsbs2Dsu7jK5AlW9c0zaYgtnYf24JckeiBe1veUVkDZkt7IFn9QNV22b/nKm3r/yONN1dnGOcQdIIw3PYz49hrNgPTD+6MBPKEpv7hYJeh00ICMAa1LmYNXmIL+MoIESxBhWkI3HCBXmnNX+Q+rsgcpKsfueuZ4x5obYYcu78v/Q33X/DIF7XK7WknQ==',
    'bkp' => '28a385b8-56a1191b-9d208736-41f49e94-a6dca14f',
    'rezim' => 'Běžný',
    'pokladna' => '11',
    'provozovna' => '22141',
    'datum' => time(),
    'Vlastní hodnota' => 'CZ12345678',
]);

// Get calculated prices (useful for EET integration)
$prices = $invoice->getFinalPrices();

// Generate first invoice (don't output yet)
$invoice->generate(false);

// ============================================
// EXAMPLE: Generate second invoice
// ============================================

$invoice->clearData();

$invoice->settings->setInvoiceNumber('16/2024');

$invoice->customer
    ->setCompany('Šmudla Jan')
    ->setStreet('Bayerova 147')
    ->setPostalCode('756 61')
    ->setCity('Rožnov pod Radhoštěm')
    ->setCompanyId('123456')
    ->setTaxId('CZ7485742569');

$issueDate2 = strtotime('2024-09-12');
$invoice->information
    ->setIssueDate(date('d.m.Y', $issueDate2))
    ->setDueDate(date('d.m.Y', $issueDate2 + 3600 * 24 * 14))
    ->setTaxableSupplyDate(date('d.m.Y', $issueDate2))
    ->addParameter('Interní označení:', 'TEST02');

$invoice->paymentDetails
    ->setPaymentMethod('Převodem')
    ->addParameter('IBAN:', 'CZ65 0800 0000 1920 0014 5399')
    ->addParameter('SWIFT:', 'KOMB CZ PP')
    ->setAccountNumber('197220727 / 0800');

$invoice->addItem('Vývoj WP pluginu', 1, 4454.55, '', 21, '');
$invoice->addItem('Programování v PHP', 50, 300, 'Hod.', 21, 'Test poznámky');

// Generate second invoice
$invoice->generate(false);

// ============================================
// EXAMPLE: Generate delivery note (non-VAT)
// ============================================

$invoice->clearData();

$invoice->settings
    ->setDocumentName('DODACÍ LIST k faktuře č. 16/2024')
    ->setVatPayer(false)
    ->setDisplayedColumns(false, true, true)
    ->setInvoiceNumber('17/2024');

$invoice->customer
    ->setCompany('Šmudla Jan')
    ->setStreet('Bayerova 147')
    ->setPostalCode('756 61')
    ->setCity('Rožnov pod Radhoštěm');

$issueDate3 = strtotime('2024-09-12');
$invoice->information
    ->setIssueDate(date('d.m.Y', $issueDate3))
    ->setDueDate(date('d.m.Y', $issueDate3 + 3600 * 24 * 14))
    ->setTaxableSupplyDate(date('d.m.Y', $issueDate3));

$invoice->paymentDetails->setPaymentMethod('Hotově');

$invoice->addItem('Vývoj WP pluginu', 1, 1000, '', 21, '');

// Generate third document
$invoice->generate(false);

// ============================================
// OUTPUT: Generate merged PDF with all invoices
// ============================================

$invoice->settings
    ->setFilename(__DIR__ . '/invoice_merged_' . date('Ymd') . '.pdf')
    ->setOutputType('FI') // Save to file and display inline
    ->setSendEmail(false);

$invoice->outputPDF();

// Get total price of last generated invoice
echo "Total price: " . $invoice->getTotalPrice() . " " . $invoice->settings->currency . "\n";
