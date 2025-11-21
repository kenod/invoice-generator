<?php declare(strict_types=1);

/**
 * Example 10: Credit Note (Opravný daňový doklad)
 *
 * This example demonstrates how to generate a credit note with:
 * - Document type set to credit note/correction
 * - Additional info with original document reference and reason
 * - Negative amounts for refund
 * - Barcode with invoice number
 * - Signature text above signature line
 * - Item spacing for better readability
 * - VAT rate labels customization
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

// Create new invoice
$invoice = new InvoiceGenerator();

// Configure settings for credit note
$invoice->getSettings()
	->setLanguage('cs')
	->setInvoiceNumber('ODD-2024/042')
	->setDocumentType(3) // 3 = Credit note/Corrective document
	->setVatPayer(true)
	->setCurrency('CZK')
	->setVatSummary(true)
	->setItemSpacing(2) // Add extra spacing between items
	->setDisplayItemCount(true)
	->setAuthor('Accounting Department')
	->setTitle('Opravný daňový doklad');

// Set custom VAT rate labels
$invoice->getSettings()->setVatRates([
	'0' => 'Nulová sazba',
	'12' => 'Snížená sazba',
	'21' => 'Základní sazba',
]);

// Set additional information - reference to original invoice and reason
$invoice->getSettings()->setAdditionalInfo(
	'2024/123', // Original invoice number
	'Chybně uvedená částka za služby. Oprava ceny položky "Programování v PHP" z 2000 Kč na 1500 Kč.'
);

// Add barcode with document number
$invoice->getSettings()->setBarcode(
	(int) str_replace(['ODD-', '/'], '', $invoice->getSettings()->getInvoiceNumber()),
	10,  // x position
	1,   // y position
	40,  // width
	12   // height
);

// Set supplier information
$invoice->getSupplier()
	->setCompany('TechSolutions s.r.o.')
	->setStreet('Innovační 789')
	->setPostalCode('160 00')
	->setCity('Praha 6')
	->setCompanyId('98765432')
	->setTaxId('CZ98765432')
	->setPhone('+420 234 567 890')
	->setEmail('faktury@techsolutions.cz')
	->setWeb('www.techsolutions.cz');

// Set customer information
$invoice->getCustomer()
	->setCompany('Klient Plus s.r.o.')
	->setStreet('Obchodní 321')
	->setPostalCode('110 00')
	->setCity('Praha 1')
	->setCompanyId('12398765')
	->setTaxId('CZ12398765');

// Set invoice dates
$originalDate = strtotime('2024-11-01');
$correctionDate = strtotime('2024-11-20');

$invoice->getInformation()
	->setIssueDate(date('d.m.Y', $correctionDate))
	->setDueDate(date('d.m.Y', $correctionDate + 3600 * 24 * 14))
	->setTaxableSupplyDate(date('d.m.Y', $originalDate))
	->addParameter('Původní doklad ze dne:', date('d.m.Y', $originalDate))
	->addParameter('Číslo objednávky:', 'PO-2024/089');

// Set payment details
$invoice->getPaymentDetails()
	->setPaymentMethod('Bankovní převod')
	->setAccountNumber('987654321/0100')
	->setBankCode('0100')
	->setVariableSymbol('2024042')
	->setConstantSymbol('0308')
	->setSpecificSymbol('456')
	->addParameter('IBAN:', 'CZ65 0800 0000 0098 7654 3210')
	->addParameter('SWIFT/BIC:', 'KOMBCZPP')
	->addParameter('Poznámka:', 'Vratka přeplatku na základě opravy');

// Add credit note items (negative amounts for refund)
// Original incorrect item was: 40 hours × 2000 CZK = 80,000 CZK
// Corrected item should be: 40 hours × 1500 CZK = 60,000 CZK
// Difference to refund: -20,000 CZK base amount

$invoice->addItem(
	'OPRAVA: Programování v PHP',
	40,
	-500, // Difference: 1500 - 2000 = -500 CZK per hour
	'hod.',
	21,
	'Oprava chybně uvedené ceny. Původní cena: 2000 Kč/hod, Správná cena: 1500 Kč/hod',
);

// Add signature text above the signature line
$invoice->getSettings()
	->setSignatureText('Opravný daňový doklad byl vystaven v souladu s § 73 zákona o DPH.', 8, 'B')
	->setSignatureText('V případě dotazů nás prosím kontaktujte na uvedeném e-mailu nebo telefonu.', 7)
	->setSignatureText('', 6) // Empty line for spacing
	->setSignatureText('S přátelským pozdravem,', 8)
	->setSignatureText('Účetní oddělení TechSolutions s.r.o.', 8, 'B');

// Add footer text
$invoice->getSettings()
	->setFooterText('TechSolutions s.r.o. je zapsána v obchodním rejstříku vedeném Městským soudem v Praze, oddíl C, vložka 123456.', 7)
	->setFooterText('Děkujeme za pochopení.', 7, 'I');

// Set styling - professional blue theme
$invoice->getSettings()
	->setStyle('fillColor', 'E8F4F8')      // Light blue background
	->setStyle('fontColor', '003D5C')       // Dark blue text
	->setStyle('pricesFillColor', 'D0E8F0')
	->setStyle('pricesFontColor', '003D5C')
	->setStyle('itemFillColor', 'F5F9FA')  // Very light blue for alternating rows
	->setStyle('signatureFillColor', 'E8F4F8');

// Enable borders for cleaner look
$invoice->getSettings()->setBorders(true, 0.2, '003D5C', 0);

// Generate and output PDF
$invoice->getSettings()
	->setFilename(__DIR__ . '/10_credit_note.pdf')
	->setOutputType('FI'); // Save to file and display inline

$invoice->generate();

echo "Credit note generated: examples/10_credit_note.pdf\n";
