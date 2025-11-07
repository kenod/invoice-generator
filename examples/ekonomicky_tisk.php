<?php
require(dirname(dirname(__FILE__))."/WFPfaktury.php");

$pdf = new \WFPfaktury\WFPfaktury();

$pdf->dodavatel->SetFirma("Web From Pixels group");
$pdf->dodavatel->SetJmeno("Petr Daněk");
$pdf->dodavatel->SetUlice("Dolní Bečva 330");
$pdf->dodavatel->SetPSC("756 55");
$pdf->dodavatel->SetMesto("Dolní Bečva");
$pdf->dodavatel->SetIC("123213");
$pdf->dodavatel->SetDIC("CZ8604142141");
$pdf->dodavatel->SetWeb("http://www.kenod.net");
$pdf->dodavatel->SetEmail("kenod@kenod.net");
$pdf->dodavatel->SetTelefon("+420 732 253 134");


$pdf->nastaveni->SetVypisovatPocetPolozek(false);
$pdf->nastaveni->SetJazyk('cs');
$pdf->nastaveni->SetFont('FreeSans');
$pdf->nastaveni->SetKonecnyPrijemceVypisovat(false);
$pdf->nastaveni->SetKonecnyPrijemceOdlisnaAdresa(true);
$pdf->nastaveni->SetPlatceDPH(true);
$pdf->nastaveni->SetZaokrouhlovatDPH(false);
$pdf->nastaveni->SetShrnutiDPH(true);
$pdf->nastaveni->SetAutor("Petr Danek");
$pdf->nastaveni->SetTitulek("Faktura");
$pdf->nastaveni->SetMena("CZK");

$pdf->nastaveni->SetReverseCharge(false);
$pdf->nastaveni->SetZaokrouhleni(0,1,1,4,false); // rozpusteni do nulove sazby
$pdf->nastaveni->SetCisloFaktury('15/2014');
$pdf->nastaveni->SetText_u_podpisu("Podnikatel je zapsán do živnostenského rejstříku.",8);
$pdf->nastaveni->SetShrnutiPrazdne(false);
$pdf->nastaveni->SetZobrazovatMJ(true);
//$pdf->nastaveni->SetCarovyKod(20140002, 164, 2, 40, 9);
$pdf->nastaveni->SetQRPlatba(true, 20, 3, 'PA', 20, 3);
$pdf->nastaveni->setPozicePoznamky('bottom');

$pdf->nastaveni->SetPodtrzeni('000000');
$pdf->nastaveni->setBorders(true, 0.3, '000000');
$pdf->nastaveni->SetStyle('fillColor', 'ffffff');
$pdf->nastaveni->SetStyle('fontColor', '000000');
$pdf->nastaveni->SetStyle('priceFillColor', 'ffffff');
$pdf->nastaveni->SetStyle('priceFontColor', '000000');
$pdf->nastaveni->SetStyle('itemFillColor','ffffff');
$pdf->nastaveni->SetStyle('itemFontColor','000000');


$pdf->odberatel->SetFirma('Kostra Jiří');
$pdf->odberatel->SetUlice('Dolní Bečva 147');
$pdf->odberatel->SetPSC('756 55');
$pdf->odberatel->SetMesto('Dolní Bečva');
$pdf->odberatel->SetIC('515454');
$pdf->odberatel->SetDIC('CZ54754241');

$vystaveni = strtotime('2014-12-22');
$pdf->informace->SetVystaveni(date("d.m.Y",$vystaveni));
$pdf->informace->SetSplatnost(date("d.m.Y",$vystaveni+3600*24*14));
$pdf->informace->SetPlneni(date("d.m.Y",$vystaveni));

$pdf->informace->AddParametr('Interní označení:', 'GP040');

$pdf->platebniUdaje->SetZpusobuhrady("Převodem");
$pdf->platebniUdaje->AddParametr("IBAN:", 'CZ65 0800 0000 1920 0014 5399');
$pdf->platebniUdaje->AddParametr("SWIFT:", 'KOMB CZ PP');
$pdf->platebniUdaje->SetCislouctu("197220727 / 0800");
//$pdf->platebniUdaje->SetKodbanky("0800");
$pdf->platebniUdaje->SetVS('');
//$pdf->platebniUdaje->SetSS('ss');
//$pdf->platebniUdaje->SetKS('052');

$pdf->pridejPolozku("Malířské a natěračské práce na hotelu Tesla",1,454,'',21, '');
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$pdf->pridejPolozku("Malířské a natěračské práce ve valašském muzeu",1,10800,'',21, '');

$ceny = $pdf->vratKonecneCeny(); // vrati celkove ceny vcetne zaokrouhleni a slev

$pdf->nastaveni->SetNazevSouboru(__DIR__."/ekonomicky_tisk.pdf");
$pdf->nastaveni->SetOutputType('FI');
$pdf->generuj();

// vrati celkovou cenu
var_dump($pdf->celkovaCena());