<?php declare(strict_types=1);

/**
 * Example 4: Translations (Slovak language)
 *
 * This example demonstrates how to generate an invoice in Slovak language.
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

// Settings with SLOVAK language
$invoice->getSettings()
	->setDisplayItemCount(false)
	->setLanguage('sk') // Set Slovak language
	->setFont('FreeSans')
	->setFinalRecipientDisplay(false)
	->setFinalRecipientDifferentAddress(true)
	->setVatPayer(true)
	->setVatSummary(true)
	->setAuthor('Petr Daněk')
	->setTitle('Faktura')
	->setCurrency('EUR') // Slovak invoices typically use EUR
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
	->setPaymentMethod('Prevodom')
	->addParameter('IBAN:', 'CZ65 0800 0000 1920 0014 5399')
	->addParameter('SWIFT:', 'KOMB CZ PP')
	->setAccountNumber('197220727 / 0800')
	->setVariableSymbol('');

// Add items
$invoice->addItem('Maliarské a natieračské práce na hoteli Tesla', 1, 454.55, '', 21, '');
$invoice->addItem('Programovanie v PHP', 50, 300, 'Hod.', 21, '- realizácia rezervačného formulára na webe xyz.cz, nastýlovanie, napojenie na PHP script');
$invoice->addItem('Maliarské a natieračské práce vo valašskom múzeu', 1, 10_800, '', 21, '');

// Output configuration
$invoice->getSettings()
	->setFilename(__DIR__ . '/04_translations.pdf')
	->setOutputType('FI');

// Generate invoice
$invoice->generate();
