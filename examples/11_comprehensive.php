<?php declare(strict_types=1);

/**
 * Example 11: Comprehensive Test - All Features
 *
 * This example demonstrates ALL available features and methods to ensure
 * the library can generate PDFs with every possible configuration.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

$invoice = new InvoiceGenerator();

// ============================================
// SETTINGS - ALL METHODS
// ============================================
$invoice->getSettings()
	->setInvoiceNumber('2024/COMP-999')
	->setLanguage('cs')
	->setDocumentType(1) // 1 = Invoice
	->setDocumentName('FAKTURA - DAŇOVÝ DOKLAD')
	->setVatPayer(true)
	->setCurrency('CZK')
	->setAuthor('Test Author')
	->setTitle('Comprehensive Invoice Test')
	->setFont('FreeSans')
	->setSendEmail(false)
	->setVatSummary(true)
	->setAmountsWithVat(false)
	->setDisplayItemCount(true)
	->setDisplayUnitColumn(true)
	->setDisplayedColumns(true, true, true) // unit, quantity, unitPrice
	->setNotePosition('top')
	->setSummaryEmpty(false)
	->setItemSpacing(1.5)
	->setAlreadyPaidInPaymentInfo(true)
	->setFinalRecipientDisplay(true)
	->setFinalRecipientDifferentAddress(true);

// Style settings
$invoice->getSettings()
	->setStyle('fillColor', 'E8F4F8')
	->setStyle('fontColor', '003D5C')
	->setStyle('pricesFillColor', 'D0E8F0')
	->setStyle('pricesFontColor', '003D5C')
	->setStyle('itemFillColor', 'F5F9FA')
	->setStyle('itemFontColor', '000000')
	->setStyle('signatureFillColor', 'E8F4F8')
	->setStyle('signatureFontColor', '003D5C');

// Borders
$invoice->getSettings()->setBorders(true, 0.3, '003D5C', 0);

// Underline
$invoice->getSettings()->setUnderline(['color' => '003D5C', 'width' => 0.2]);

// VAT rates with custom labels
$invoice->getSettings()->setVatRates([
	'0' => 'Nulová sazba DPH',
	'12' => 'Snížená sazba DPH',
	'21' => 'Základní sazba DPH',
]);

// Discount
$invoice->getSettings()->setDiscount(
	10.5,  // percentage
	500,   // fixed amount
	2,     // vatId (12%)
	100,   // rounding
	21     // vatIdRounding
);

// Rounding
$invoice->getSettings()->setRounding(
	1,     // type (1 = to crowns)
	1,     // method
	1,     // mode
	4,     // vatId for rounding (0%)
	true   // hideZeroVat
);

// Deposits
$invoice->getSettings()->setDeposits(5000.50);

// QR Payment
$invoice->getSettings()->setQRPayment(
	true,  // enabled
	20,    // x position
	3,     // y position
	'PA',  // type
	30,    // size
	5      // margin
);

// Barcode
$invoice->getSettings()->setBarcode(
	2024999,  // code
	10,       // x
	1,        // y
	40,       // width
	12        // height
);

// Images
$invoice->getSettings()->setImage(
	__DIR__ . '/../vendor/tecnickcom/tcpdf/examples/images/tcpdf_logo.jpg',
	80,   // horizontal (x)
	10,   // vertical (y)
	'A',  // repeat (All pages)
	30,   // width
	15    // height
);

// Signature texts
$invoice->getSettings()
	->setSignatureText('Toto je podpisový text.', 9, 'B')
	->setSignatureText('Další řádek podpisu.', 8, 'I')
	->setSignatureText('Třetí řádek podpisu.', 8);

// Footer texts
$invoice->getSettings()
	->setFooterText('Patička první řádek - tučně', 8, 'B')
	->setFooterText('Patička druhý řádek - kurzívou', 7, 'I')
	->setFooterText('Patička třetí řádek - normálně', 7);

// EET data
$invoice->getSettings()->setEET([
	'fik' => 'test-fik-code-12345678-90ab-cdef',
	'pkp' => 'TEST_PKP_CODE_SIGNATURE',
	'bkp' => 'AABBCCDD-EEFFGGHH-IIJJKKLL-MMNNOOPP-QQRRSSTT',
	'rezim' => 'Běžný',
	'pokladna' => '1',
	'provozovna' => '101',
	'datum' => time(),
	'Custom Field' => 'Custom Value',
]);

// ============================================
// SUPPLIER - ALL METHODS
// ============================================
$invoice->getSupplier()
	->setCompany('Kompletní testovací firma s.r.o.')
	->setName('Jméno dodavatele')
	->setStreet('Testovací ulice 123')
	->setPostalCode('110 00')
	->setCity('Praha')
	->setCountry('Česká republika')
	->setCompanyId('12345678')
	->setTaxId('CZ12345678')
	->setPhone('+420 123 456 789')
	->setEmail('dodavatel@example.cz')
	->setWeb('www.dodavatel-test.cz');

// ============================================
// CUSTOMER - ALL METHODS
// ============================================
$invoice->getCustomer()
	->setCompany('Odběratel Testing Ltd.')
	->setName('Kontaktní osoba odběratele')
	->setStreet('Obchodní 456')
	->setPostalCode('602 00')
	->setCity('Brno')
	->setCountry('Česká republika')
	->setCompanyId('87654321')
	->setTaxId('CZ87654321')
	->setPhone('+420 987 654 321')
	->setEmail('odberatel@example.cz')
	->setWeb('www.odberatel-test.cz');

// ============================================
// END RECIPIENT (Final Recipient) - ALL METHODS
// ============================================
$invoice->getEndRecipient()
	->setCompany('Konečný příjemce s.r.o.')
	->setName('Jméno konečného příjemce')
	->setStreet('Konečná 789')
	->setPostalCode('702 00')
	->setCity('Ostrava')
	->setCountry('Česká republika')
	->setCompanyId('11122233')
	->setTaxId('CZ11122233')
	->setPhone('+420 111 222 333')
	->setEmail('konecny@example.cz')
	->setWeb('www.konecny-prijemce.cz');

// ============================================
// INFORMATION - ALL METHODS
// ============================================
$invoice->getInformation()
	->setOrder('OBJ-2024/999')
	->setFromDate('01.01.2024')
	->setIssueDate('15.11.2024')
	->setDueDate('29.11.2024')
	->setTaxableSupplyDate('15.11.2024')
	->addParameter('Dodací list:', 'DL-2024/555')
	->addParameter('Zakázka:', 'Z-2024/777')
	->addParameter('Smlouva:', 'SM-2024/888')
	->addParameter('Objednávka:', 'OBJ-2024/999')
	->addParameter('Custom parameter 1:', 'Custom value 1')
	->addParameter('Custom parameter 2:', 'Custom value 2');

// ============================================
// PAYMENT DETAILS - ALL METHODS
// ============================================
$invoice->getPaymentDetails()
	->setPaymentMethod('Bankovní převod')
	->setAccountNumber('123456789/0100')
	->setBankCode('0100')
	->setVariableSymbol('2024999')
	->setConstantSymbol('0308')
	->setSpecificSymbol('999888')
	->addParameter('IBAN:', 'CZ65 0800 0000 1234 5678 9012')
	->addParameter('SWIFT/BIC:', 'KOMBCZPP')
	->addParameter('Banka:', 'Komerční banka, a.s.')
	->addParameter('Poznámka k platbě:', 'Testovací platba - kompletní test')
	->addParameter('Custom payment 1:', 'Custom value')
	->addParameter('Custom payment 2:', 'Another custom value');

// ============================================
// ITEMS - ALL METHODS
// ============================================

// Item 1: All parameters with note
$invoice->addItem(
	'Položka 1 - Kompletní konfigurace',
	10.5,
	1250.75,
	'ks',
	21,
	'Poznámka k položce 1: Toto je velmi dlouhá poznámka, která testuje zalamování textu a zobrazení na více řádcích.'
);

// Item 2: Different VAT rate
$invoice->addItem(
	'Položka 2 - Snížená sazba DPH',
	5,
	500,
	'hod',
	12,
	'Poznámka k položce 2 se sníženou sazbou DPH.'
);

// Item 3: Zero VAT
$invoice->addItem(
	'Položka 3 - Nulová sazba DPH',
	1,
	1000,
	'měs',
	0,
	'Poznámka k položce 3 s nulovou sazbou.'
);

// Item 4: Without note
$invoice->addItem(
	'Položka 4 - Bez poznámky',
	100,
	25.50,
	'm',
	21
);

// Item 5: Fractional quantities
$invoice->addItem(
	'Položka 5 - Desetinné množství',
	2.75,
	899.99,
	'kg',
	21,
	'Testování desetinných čísel v množství i ceně.'
);

// Item 6: Large numbers
$invoice->addItem(
	'Položka 6 - Velká čísla',
	1000,
	5000,
	'ks',
	21,
	'Test velkých částek.'
);

// Item 7: Small numbers
$invoice->addItem(
	'Položka 7 - Malá čísla',
	0.5,
	10.25,
	'l',
	12
);

// Item 8: Very long name
$invoice->addItem(
	'Položka 8 - Velmi dlouhý název položky, která testuje zalamování textu v názvu položky na více řádků, aby se ověřilo správné zobrazení',
	1,
	999.99,
	'sada',
	21,
	'Poznámka k dlouhému názvu.'
);

// ============================================
// CUSTOM CALLBACK
// ============================================
$invoice->setCustomCallback(function ($pdf) {
	// Add custom text using TCPDF methods
	$pdf->SetFont('FreeSans', 'B', 10);
	$pdf->SetTextColor(255, 0, 0); // Red
	$pdf->Text(10, 280, 'Custom Callback Text - Added via callback function');
	$pdf->SetTextColor(0, 0, 0); // Reset to black
});

$invoice->getSettings()
	->setFilename(__DIR__ . '/11_comprehensive.pdf')
	->setOutputType('FI'); // Save to file and display inline

// ============================================
// GENERATE PDF
// ============================================
$invoice->generate();