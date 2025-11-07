<?php
require(dirname(dirname(__FILE__))."/WFPfaktury.php");

$pdf = new \WFPfaktury\WFPfaktury();

include __DIR__.'/inc/zakladni_nastaveni.php';

$pdf->pridejPolozku("Malířské a natěračské práce na hotelu Tesla",1,454.55,'',21, '');
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$pdf->pridejPolozku("Malířské a natěračské práce ve valašském muzeu",1,10800,'',21, '');

/**
 * Vypíše custom text bold fontem velikosti 20 zarovnaný na střed na souřadnicích 10,220
 * Lze volat jakékoliv funkce TCPDF
 * @param WFPfaktury class instance $pdf
 */
function MyCustomCallback($pdf) {
	$pdf->SetFont('dejavusans', 'B', 20);
	$pdf->SetXY(10,220);
	$pdf->Cell(190, 8, 'Custom callback text', 0, 0, 'C');
}
$pdf->customCallback = 'MyCustomCallback';

// ------------ NASTAVENI VYSTUPU A VYGENEROVANI
$pdf->nastaveni->SetNazevSouboru(__DIR__."/custom_callback.pdf");
$pdf->nastaveni->SetOutputType('FI');

// vygeneruj prvni fakturu
$pdf->generuj();