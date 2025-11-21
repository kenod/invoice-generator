<?php declare(strict_types=1);

/**
 * Example 1: Basic Invoice
 *
 * This example demonstrates how to generate a simple invoice with minimal configuration.
 * It includes the essential elements: supplier, customer, items, and payment details.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Kenod\InvoiceGenerator\InvoiceGenerator;

// Create new invoice
$invoice = new InvoiceGenerator();

// Configure basic settings
$invoice->getSettings()
	->setLanguage('cs')
	->setInvoiceNumber('2024001')
	->setVatPayer(true)
	->setCurrency('CZK');

// Set supplier information (your company)
$invoice->getSupplier()
	->setCompany('My Company s.r.o.')
	->setStreet('Main Street 123')
	->setPostalCode('110 00')
	->setCity('Prague')
	->setCompanyId('12345678')
	->setTaxId('CZ12345678')
	->setPhone('+420 123 456 789')
	->setEmail('info@mycompany.cz');

// Set customer information
$invoice->getCustomer()
	->setCompany('Customer Ltd.')
	->setStreet('Customer Street 456')
	->setPostalCode('602 00')
	->setCity('Brno')
	->setCompanyId('87654321')
	->setTaxId('CZ87654321');

// Set invoice dates
$invoice->getInformation()
	->setIssueDate(date('d.m.Y'))
	->setDueDate(date('d.m.Y', strtotime('+14 days')))
	->setTaxableSupplyDate(date('d.m.Y'));

// Set payment details
$invoice->getPaymentDetails()
	->setPaymentMethod('Bank transfer')
	->setAccountNumber('123456789/0100')
	->setVariableSymbol('2024001');

// Add invoice items
$invoice->addItem('Web development', 40, 1500, 'hours', 21);
$invoice->addItem('Hosting services', 1, 5000, 'year', 21);
$invoice->addItem('Domain registration', 1, 300, 'year', 21);

// Generate and output PDF
$invoice->getSettings()
	->setFilename(__DIR__ . '/01_basic_invoice.pdf')
	->setOutputType('FI'); // Save to file and display inline

$invoice->generate();
