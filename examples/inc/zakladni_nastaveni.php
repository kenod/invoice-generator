<?php
//$pdf->dodavatel->SetFirma("Web From Pixels group");
$pdf->dodavatel->SetJmeno("Petr Daněk");
$pdf->dodavatel->SetUlice("Dolní Bečva 330");
$pdf->dodavatel->SetPSC("756 55");
$pdf->dodavatel->SetMesto("Dolní Bečva");
$pdf->dodavatel->SetIC("123213");
$pdf->dodavatel->SetDIC("CZ8604142141");
//$pdf->dodavatel->SetWeb("http://www.kenod.net");
//$pdf->dodavatel->SetEmail("kenod@kenod.net");
//$pdf->dodavatel->SetTelefon("+420 732 253 134");

/*
$pdf->nastaveni->SetPodpis('tcpdf.crt');
$pdf->nastaveni->SetPodpisHeslo('tcpdfdemo');
$pdf->nastaveni->SetPodpisInfo('WFPfaktury','Office','Testovani podpisu','kenod@kenod.net');
*/
$pdf->nastaveni->SetVypisovatPocetPolozek(false);
$pdf->nastaveni->SetJazyk('cs');
$pdf->nastaveni->SetFont('FreeSans');
$pdf->nastaveni->SetKonecnyPrijemceVypisovat(false);
$pdf->nastaveni->SetKonecnyPrijemceOdlisnaAdresa(true);
$pdf->nastaveni->SetPlatceDPH(true);
$pdf->nastaveni->SetZaokrouhlovatDPH(false);
$pdf->nastaveni->SetShrnutiDPH(true);
//$pdf->nastaveni->SetSazbyDPH(array('0'=>'Nulová sazba', '15'=>'Snizena sazba', '21'=>'Základní sazba'));
$pdf->nastaveni->SetAutor("Petr Danek");
$pdf->nastaveni->SetTitulek("Faktura");
$pdf->nastaveni->SetMena("CZK");
//$pdf->nastaveni->SetVzdalenost_polozek(5);
$pdf->nastaveni->SetReverseCharge(false);
$pdf->nastaveni->SetZaokrouhleni(1,1,1,4,false); // rozpusteni do nulove sazby
//$pdf->nastaveni->SetTypDokladu('odd');
//$pdf->nastaveni->SetDoplnujiciInformace(2154214, 'Odstoupení zákazníka od kupní smlouvy ve 14 denní zákonné lhůtě a to dne 21. 5. 2014.');
//$pdf->nastaveni->SetUhrazeno_zalohou(4523);
$pdf->nastaveni->SetCislo_faktury('15/2014');
//	var $text_podpis = array(); // text vlevo od podpisu (text,[velikost=8],[styl UBI])
$pdf->nastaveni->SetText_u_podpisu("Podnikatel je zapsán do živnostenského rejstříku.",8);
//$pdf->nastaveni->SetText_konec("No a tady si můžu rovněž vypsat nějaký text, ovšem jen jednořádkový. Např. Fakturu vystavil Petr Daněk dne 03.01.2012");
//$pdf->nastaveni->SetObrazek('logo_wfp.jpg', 10, 5, 'A');
//$pdf->nastaveni->SetObrazek('demo.png', 80, 60, 'A');
//$pdf->nastaveni->SetObrazek('logo-pes.jpg', 65, 35, 'F');
$pdf->nastaveni->SetShrnutiPrazdne(false);
$pdf->nastaveni->SetZobrazovatMJ(false);
//$pdf->nastaveni->SetCarovyKod(20140002, 164, 2, 40, 9);
$pdf->nastaveni->SetQRPlatba(true, 20, 3, 'PA', 20, 3);
$pdf->nastaveni->setPozicePoznamky('top');

//$pdf->nastaveni->SetSleva(0,1,2,1); // prida slevu 10 % a tu vypise jak v polozkach, tak dole, a vypsala by se i kdyby byla nulova
//$pdf->nastaveni->SetSleva(500,0,0,1);


$pdf->nastaveni->SetStyle('fillColor', '999999');
$pdf->nastaveni->SetStyle('fontColor', 'ffffff');
$pdf->nastaveni->SetStyle('priceFillColor', '999999');
$pdf->nastaveni->SetStyle('priceFontColor', 'ffffff');
$pdf->nastaveni->SetStyle('itemFillColor','dddddd');
$pdf->nastaveni->SetStyle('itemFontColor','000000');

$pdf->odberatel->SetFirma('Kostra Jiří');
$pdf->odberatel->SetUlice('Dolní Bečva 147');
$pdf->odberatel->SetPSC('756 55');
$pdf->odberatel->SetMesto('Dolní Bečva');
$pdf->odberatel->SetIC('515454');
$pdf->odberatel->SetDIC('CZ54754241');

//$pdf->konecny_prijemce->SetFirma('Jiří Šmela');
//$pdf->konecny_prijemce->SetUlice('Náměstí TGM 5478/54');
//$pdf->konecny_prijemce->SetPSC('760 01');
//$pdf->konecny_prijemce->SetMesto('Zlín');

//$pdf->informace->SetObjednavka('id1');
//$pdf->informace->SetZedne(date("d.m.Y",time()));
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
//$pdf->platebni_udaje->SetSS('ss');
//$pdf->platebni_udaje->SetKS('052');