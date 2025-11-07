<?php
require(dirname(dirname(__FILE__))."/WFPfaktury.php");

$pdf = new \WFPfaktury\WFPfaktury();

include __DIR__.'/inc/zakladni_nastaveni.php';

$pdf->nastaveni->SetObrazek('logo_wfp.jpg', 10, 5, 'A');
$pdf->nastaveni->SetObrazek('demo.png', 50, 220, 'L');
$pdf->nastaveni->SetObrazek('logo-pes.jpg', 65, 35, 'F');

$pdf->pridejPolozku("Malířské a natěračské práce na hotelu Tesla",1,454.55,'',21, '');
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$pdf->pridejPolozku("Malířské a natěračské práce ve valašském muzeu",1,10800,'',21, '');

// ------------ NASTAVENI VYSTUPU A VYGENEROVANI
$pdf->nastaveni->SetNazevSouboru(__DIR__."/obrazky.pdf");
$pdf->nastaveni->SetOutputType('FI');

// vygeneruj prvni fakturu
$pdf->generuj();