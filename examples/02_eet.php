<?php declare(strict_types=1);

/**
 * Example 2: EET (Electronic Registration of Sales)
 *
 * This example demonstrates how to include EET information on an invoice.
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
 * EET Integration Example
 *
 * This shows how you would integrate EET using FilipSedivy/PHP-EET library:
 * https://github.com/filipsedivy/PHP-EET
 *
 * Example code (commented out - requires library installation):
 *
 * $prices = $invoice->getFinalPrices();
 *
 * require_once 'vendor/autoload.php';
 * $certificate = new FilipSedivy\EET\Certificate('path/to/cert', 'password');
 * $dispatcher = new FilipSedivy\EET\Dispatcher(FilipSedivy\EET\Dispatcher::PLAYGROUND, $certificate);
 *
 * $uuid = FilipSedivy\EET\Utils\UUID::v4();
 * $receipt = new FilipSedivy\EET\Receipt;
 * $receipt->uuid_zpravy = $uuid;
 * $receipt->id_provoz = 1;
 * $receipt->id_pokl = 1;
 * $receipt->dic_popl = 'CZ123456789';
 * $receipt->porad_cis = 117001;
 * $receipt->dat_trzby = new \DateTime();
 * $receipt->celk_trzba = (float) $prices['totalWithVat'];
 * $receipt->prvni_zaslani = 1;
 *
 * if (isset($prices['vatSummary'][0])) {
 *     $receipt->zakl_nepodl_dph = $prices['vatSummary'][0]['base'];
 * }
 * if (isset($prices['vatSummary'][21])) {
 *     $receipt->zakl_dan1 = $prices['vatSummary'][21]['base'];
 *     $receipt->dan1 = $prices['vatSummary'][21]['vat'];
 * }
 *
 * try {
 *     $fik = $dispatcher->send($receipt);
 *     $bkp = $dispatcher->getBkp();
 *     $pkp = $dispatcher->getPkp();
 * } catch (\FilipSedivy\EET\Exceptions\ServerException $e) {
 *     // Handle error
 *     $bkp = $dispatcher->getBkp();
 *     $pkp = $dispatcher->getPkp();
 * }
 *
 * $invoice->settings->setEET([
 *     'fik' => $fik,
 *     'pkp' => $pkp,
 *     'bkp' => $bkp,
 *     'rezim' => 'Běžný',
 *     'pokladna' => '11',
 *     'provozovna' => '22141',
 *     'datum' => time(),
 *     'Vlastní hodnota' => 'CZ12345678',
 * ]);
 */

// For demo purposes, use static EET data
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

// Output configuration
$invoice->getSettings()
	->setFilename(__DIR__ . '/02_eet.pdf')
	->setOutputType('FI');

// Generate invoice
$invoice->generate();
