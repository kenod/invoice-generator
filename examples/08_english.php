<?php declare(strict_types=1);

/**
 * Example 8: English translation
 *
 * This example demonstrates how to use English language for invoices.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

// Create invoice instance
$invoice = new InvoiceGenerator();

// Settings with English language
$invoice->getSettings()
	->setInvoiceNumber('2024001')
	->setLanguage('en') // Set English language
	->setVatPayer(true)
	->setRounding(0, 0, 0, 0, false);

// Supplier address
$invoice->getSupplier()
	->setName('Tech Solutions Ltd.')
	->setStreet('123 Innovation Street')
	->setCity('Prague')
	->setPostalCode('110 00')
	->setCountry('Czech Republic')
	->setCompanyId('12345678')
	->setTaxId('CZ12345678')
	->setEmail('billing@techsolutions.example')
	->setPhone('+420 123 456 789')
	->setWeb('www.techsolutions.example')
	->translate();

// Customer address
$invoice->getCustomer()
	->setCompany('Global Corp Inc.')
	->setStreet('456 Business Avenue')
	->setCity('London')
	->setPostalCode('SW1A 1AA')
	->setCountry('United Kingdom')
	->setCompanyId('GB987654321')
	->translate();

// Invoice information
$invoice->getInformation()
	->setOrder('PO-2024-001')
	->setFromDate('2024-01-15')
	->setIssueDate('2024-01-20')
	->setDueDate('2024-02-05')
	->setTaxableSupplyDate('2024-01-20')
	->translate();

// Payment details
$invoice->getPaymentDetails()
	->setPaymentMethod('Bank transfer')
	->setAccountNumber('123456789')
	->setBankCode('0100')
	->setVariableSymbol('2024001')
	->translate();

// Add invoice items
$invoice->addItem('Software Development Services', 40, 1250, 'hours', 21);
$invoice->addItem('System Integration', 20, 1500, 'hours', 21);
$invoice->addItem('Technical Consultation', 10, 1000, 'hours', 21);

// Add footer text
$invoice->getSettings()
	->setFooterText('Payment terms: Net 15 days', 8)
	->setFooterText('Thank you for your business!', 8);

// Generate and output PDF
$invoice->generate(false);
$invoice->getSettings()
	->setFilename(__DIR__ . '/08_english.pdf')
	->setOutputType('FI');
$invoice->outputPDF();

echo "Invoice generated: examples/08_english.pdf\n";
