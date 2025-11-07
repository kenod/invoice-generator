<?php
require(dirname(dirname(__FILE__))."/WFPfaktury.php");

$pdf = new \WFPfaktury\WFPfaktury();

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
$pdf->nastaveni->SetCisloFaktury('15/2014');
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


$pdf->nastaveni->SetStyle('fillColor', 'CC0021');
$pdf->nastaveni->SetStyle('fontColor', 'ffffff');
$pdf->nastaveni->SetStyle('priceFillColor', 'CC0021');
$pdf->nastaveni->SetStyle('priceFontColor', 'ffffff');
$pdf->nastaveni->SetStyle('itemFillColor','ffd6d6');
$pdf->nastaveni->SetStyle('itemFontColor','000000');
/*
$pdf->nastaveni->SetPodtrzeni('000000');
$pdf->nastaveni->setBorders(true, 0.3, '000000');
$pdf->nastaveni->SetStyle('fillColor', 'ffffff');
$pdf->nastaveni->SetStyle('fontColor', '000000');
$pdf->nastaveni->SetStyle('priceFillColor', 'ffffff');
$pdf->nastaveni->SetStyle('priceFontColor', '000000');
$pdf->nastaveni->SetStyle('itemFillColor','ffffff');
//$pdf->nastaveni->SetStyle('itemFillColor',false);
$pdf->nastaveni->SetStyle('itemFontColor','000000');
*/

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
//$pdf->platebniUdaje->SetSS('ss');
//$pdf->platebniUdaje->SetKS('052');

$pdf->pridejPolozku("Malířské a natěračské práce na hotelu Tesla",1,454.55,'',21, '');
//$pdf->pridejPolozku("Misto na serveru; ".$delka." mesicu",$text_value["nazev_programu"]." - ".prevednabajty($text_value['velikost']),$text_value["cena"]));
//$pdf->pridejPolozku("Nejaky nazev",1,1000,'kg',0);
//$pdf->pridejPolozku("Nejaky nazev",1,1470.25,'Ks',14);
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
//$pdf->pridejPolozku("Prodej zpětného odkazu",1,500,'Ks',21, 'na webu xyz.cz, na 12 měsíců');
//$pdf->pridejPolozku("Klávesnice",1,199,'Ks',21, 'BestKey, rozšířená záruka', 12345);
//$pdf->pridejPolozku("Nejaky nazev, který se ovšem asi nevleze na jeden řádek a tak zabere dva, možná i tři řádky tak zabere dva, možná i tři řádky.",1,10547.25,'Ks',20);
//$pdf->pridejPolozku("Nejaky nazev",1,335,'Ks',0);

$pdf->pridejPolozku("Malířské a natěračské práce ve valašském muzeu",1,10800,'',21, '');
//$pdf->pridejPolozku("Malířské a natěračské práce v Uhor Valašské Meziříčí",1,9115,'',21, '');

/*
$pdf->email->SetAddress("kenod@kenod.net");
$pdf->email->SetPhp_mailer_path("faktury/PHPMailer/class.phpmailer.php");
$pdf->email->SetFrom_name("Fakturační email");
$pdf->email->SetFrom("faktury@priklad.cz");
$pdf->email->SetSubject("Posíláme Vám fakturu za služby - dobití peněženky.");
$pdf->email->SetBody("V příloze Vám zasíláme fakturu ...

  tak si ji uložte.
  ");
*/

$ceny = $pdf->vratKonecneCeny(); // vrati celkove ceny vcetne zaokrouhleni a slev
/**
 * -------------- PRIKLAD EET NAPOJENI -----------
 * Priklad pouziti implementace EET postavene na knihovne od Filipa Sediveho: https://github.com/filipsedivy/PHP-EET
 * Priklad slouzi pouze jako ukazka mozne implementace do tridy WFPfaktury. Pro zprovozneni je potreba vyse zminenou knihovnu nainstalovat a priklad upravit pro dosazeni spravne funkcnosti

require_once(APP_DIR . "includes/eet/autoload.php");
$certificate = new FilipSedivy\EET\Certificate('cesta_k_certifikatu', 'heslo_k_certifikatu');
$dispatcher = new FilipSedivy\EET\Dispatcher(Playground, $certificate);
$uuid = FilipSedivy\EET\Utils\UUID::v4(); // Generování UUID
$r = new FilipSedivy\EET\Receipt;
$r->uuid_zpravy = $uuid;
$r->id_provoz = 1;
$r->id_pokl = 1;
$r->dic_popl = 'CZ123456789';
$r->porad_cis = 117001;
$r->dat_trzby = new \DateTime();
$r->celk_trzba = (float) $ceny['cena_celkem'];
$r->prvni_zaslani = 1;
if (isset($ceny['shrnuti_dph'][0])) {
	$r->zakl_nepodl_dph = $ceny['shrnuti_dph'][0]['zaklad'];
}
if (isset($dph[21])) {
	$r->zakl_dan1 = $ceny['shrnuti_dph'][21]['zaklad'];
	$r->dan1 = $ceny['shrnuti_dph'][21]['dph'];
}
if (isset($ceny['shrnuti_dph'][15])) {
	$r->zakl_dan2 = $ceny['shrnuti_dph'][15]['zaklad'];
	$r->dan2 = $ceny['shrnuti_dph'][15]['dph'];
}
if (isset($ceny['shrnuti_dph'][10])) {
	$r->zakl_dan3 = $ceny['shrnuti_dph'][10]['zaklad'];
	$r->dan3 = $ceny['shrnuti_dph'][10]['dph'];
}
try {
	$fik = $dispatcher->send($r);
	$bkp = $dispatcher->getBkp();
	$pkp = $dispatcher->getPkp();
} catch (\FilipSedivy\EET\Exceptions\ServerException $e) {
	if ($e->getCode() > 0) {
		die('Uctenku se nepodarilo odeslat, protoze: '.$e->getMessage());
	}
	$bkp = $dispatcher->getBkp();
	$pkp = $dispatcher->getPkp();
} catch (\Exception $e) {
	$bkp = $dispatcher->getBkp();
	$pkp = $dispatcher->getPkp();
	if (empty($bkp) || empty($pkp)) {
		die('Uctenku se nepodarilo odeslat, protoze: '.$e->getMessage());
	}
}
$pdf->nastaveni->seteet(array(
	'fik' => 'fac367af-019c-4fc8-a747-dbff21b07235-ff', 
	'pkp' => 'G/x3I4cOcy5nYnui+4TMrktpKY55+h2sUe2SO7QB92TW8krvWuIVyuiE8qONeBnFrHwWaN3kzP5HWn6zGJHnaxp0SFr7KTyNaYSAr4h6Ef/lZ8bBTdPYo+Lq8CZW/q7Q91mAGwN+CFyyOWlGJr8lsBt8cO6zsbs2Dsu7jK5AlW9c0zaYgtnYf24JckeiBe1veUVkDZkt7IFn9QNV22b/nKm3r/yONN1dnGOcQdIIw3PYz49hrNgPTD+6MBPKEpv7hYJeh00ICMAa1LmYNXmIL+MoIESxBhWkI3HCBXmnNX+Q+rsgcpKsfueuZ4x5obYYcu78v/Q33X/DIF7XK7WknQ==', 
	'bkp' => '28a385b8-56a1191b-9d208736-41f49e94-a6dca14f', 
	'rezim' => 'Běžný', 
	'pokladna' => '11', 
	'provozovna' => '22141', 
	'datum' => time(), 
	'Vlastní hodnota' => 'CZ12345678'
	)
);
* ------------- KONEC PRIKLADU EET NAPOJENI -----------------
*/
$pdf->nastaveni->seteet(array(
	'fik' => 'fac367af-019c-4fc8-a747-dbff21b07235-ff', 
	'pkp' => 'G/x3I4cOcy5nYnui+4TMrktpKY55+h2sUe2SO7QB92TW8krvWuIVyuiE8qONeBnFrHwWaN3kzP5HWn6zGJHnaxp0SFr7KTyNaYSAr4h6Ef/lZ8bBTdPYo+Lq8CZW/q7Q91mAGwN+CFyyOWlGJr8lsBt8cO6zsbs2Dsu7jK5AlW9c0zaYgtnYf24JckeiBe1veUVkDZkt7IFn9QNV22b/nKm3r/yONN1dnGOcQdIIw3PYz49hrNgPTD+6MBPKEpv7hYJeh00ICMAa1LmYNXmIL+MoIESxBhWkI3HCBXmnNX+Q+rsgcpKsfueuZ4x5obYYcu78v/Q33X/DIF7XK7WknQ==', 
	'bkp' => '28a385b8-56a1191b-9d208736-41f49e94-a6dca14f', 
	'rezim' => 'Běžný', 
	'pokladna' => '11', 
	'provozovna' => '22141', 
	'datum' => time(), 
	'Vlastní hodnota' => 'CZ12345678'
	)
);
// vygeneruj prvni fakturu
$pdf->generuj(false);

// ------------ DATA PRO DRUHOU
$pdf->clearData();

$pdf->nastaveni->SetCisloFaktury('16/2018');

$pdf->odberatel->SetFirma('Šmudla Jan');
$pdf->odberatel->SetUlice('Bayerova 147');
$pdf->odberatel->SetPSC('756 61');
$pdf->odberatel->SetMesto('Rožnov pod Radhoštěm');
$pdf->odberatel->SetIC('123456');
$pdf->odberatel->SetDIC('CZ7485742569');

//$pdf->konecny_prijemce->SetFirma('Jiří Šmela');

//$pdf->informace->SetObjednavka('id1');
//$pdf->informace->SetZedne(date("d.m.Y",time()));
$vystaveni = strtotime('2018-09-12');
$pdf->informace->SetVystaveni(date("d.m.Y",$vystaveni));
$pdf->informace->SetSplatnost(date("d.m.Y",$vystaveni+3600*24*14));
$pdf->informace->SetPlneni(date("d.m.Y",$vystaveni));

$pdf->informace->AddParametr('Interní označení:', 'TEST02');

$pdf->platebniUdaje->SetZpusobuhrady("Převodem");
$pdf->platebniUdaje->AddParametr("IBAN:", 'CZ65 0800 0000 1920 0014 5399');
$pdf->platebniUdaje->AddParametr("SWIFT:", 'KOMB CZ PP');
$pdf->platebniUdaje->SetCislouctu("197220727 / 0800");
//$pdf->platebniUdaje->SetKodbanky("0800");
//$pdf->platebniUdaje->SetSS('ss');
//$pdf->platebniUdaje->SetKS('052');

$pdf->pridejPolozku("Vývoj WP pluginu",1,4454.55,'',21, '');
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, 'Test poznámky');

// vygeneruj druhou fakturu
$pdf->generuj(false);

// ------------ DATA PRO TRETI FAKTURU
$pdf->clearData();
$pdf->nastaveni->setNazevDokladu('DODACÍ LIST k faktuře č. 16/2018');
$pdf->nastaveni->SetCisloFaktury('17/2018');
$pdf->odberatel->SetFirma('Šmudla Jan');
$pdf->odberatel->SetUlice('Bayerova 147');
$pdf->odberatel->SetPSC('756 61');
$pdf->odberatel->SetMesto('Rožnov pod Radhoštěm');

$vystaveni = strtotime('2018-09-12');
$pdf->informace->SetVystaveni(date("d.m.Y",$vystaveni));
$pdf->informace->SetSplatnost(date("d.m.Y",$vystaveni+3600*24*14));
$pdf->informace->SetPlneni(date("d.m.Y",$vystaveni));
$pdf->platebniUdaje->SetZpusobuhrady("Hotově");

$pdf->pridejPolozku("Vývoj WP pluginu",1,1000,'',21, '');
// vygeneruj treti fakturu
$pdf->generuj(false);

// ------------ NASTAVENI VYSTUPU A VYGENEROVANI
$pdf->nastaveni->SetNazevSouboru(__DIR__."/vice_dokladu.pdf");
$pdf->nastaveni->SetOutputType('FI');
$pdf->nastaveni->SetSend_email(false);
$pdf->outputPDF();

// vrati celkovou cenu
var_dump($pdf->celkovaCena());