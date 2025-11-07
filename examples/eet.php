<?php
require(dirname(dirname(__FILE__))."/WFPfaktury.php");

$pdf = new \WFPfaktury\WFPfaktury();

include __DIR__.'/inc/zakladni_nastaveni.php';

$pdf->pridejPolozku("Malířské a natěračské práce na hotelu Tesla",1,454.55,'',21, '');
$pdf->pridejPolozku("Programování v PHP",50,300,'Hod.',21, '- realizace rezervačního formuláře na webu xyz.cz, nastylování, napojení na PHP script');
$pdf->pridejPolozku("Malířské a natěračské práce ve valašském muzeu",1,10800,'',21, '');

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

// ------------ NASTAVENI VYSTUPU A VYGENEROVANI
$pdf->nastaveni->SetNazevSouboru(__DIR__."/eet.pdf");
$pdf->nastaveni->SetOutputType('FI');

// vygeneruj prvni fakturu
$pdf->generuj();
