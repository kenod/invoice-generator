<?php

/* * ****************************************************************************************************
 * ******************************************************************************************************
  Třída 	:	WFPfaktury
  Verze 	:	2.4
  Revize	:	002
  Datum poslední úpravy	:	22. 10. 2024
  Autor:	:	Petr Daněk
  Kontakt	:	danek@chci-www.cz / www.danekpetr.cz

  Není dovoleno šířit tuto třídu mezi třetí osoby, které nemají zakoupenu licenci na její používání.
  Rovněž není dovoleno šířit části kódů.
  Je dovoleno libovolně upravovat třídu, ale ani po úpravách není možné vydávat třídu za své dílo a
  rozšiřovat ji. Pokud není písemně uděleno svolení, není možné odstranit, nebo změnit text o generátoru faktury v patičce faktury.
  Více informací o licenci na http://faktury.chci-www.cz/licence

  Novinky v revizích:
  002
  - Přidána možnost vypnout MJ.
  003
  - Přidána možnost slev.
  004
  - Přidána možnost odeslání faktury mailem.
  - Opraveny chyby související se změnou vstupního kódování na jiné než UTF-8.
  - Změněny výchozí sazby DPH dle nového zákoka o DPH pro rok 2010.
  005
  - Oprava výpočtu zaokrouhlování DPH.
  - Oprava manuálního nastavení vlastních sazeb DPH.
  - Přidána možnost nezobrazovat nulové položky u sazeb DPH při zaokrouhlování a shrnutí DPH.
 * Verze 1.3
  001
  - Nový PDF generátor - TCPDF
  - Možnost elektronického podpisu PDF
  002
  - Ošetření NOTICE vyjimek
  - Přidání viditelnosti k metodám a proměným
 * Verze 2.0
  001
  - Nový vzhled faktur
  - Možnost vložení více obrázků
  - Možnost definování barev pozadí a písma u určitých prvků
  - Upraveny defaultní sazby DPH, dle zákonů pro rok 2012
  002
  - Opraveno vykreslování podbarvení a podtržení položek, opraveno zaokrouhlování a výpis ve shrnutí DPH u neceločíselných základů
  - Upraveno zaokrouhlování
 * Verze 2.1
  001
  - Podpora více jazyků přes lokalizační soubory
  - Přidána možnost vložení QR platby
  - Možnost zadat k položce faktury popis a čárový kód
  - Přidán koncový příjemce
  002
  - Možnost určit způsob zaokrouhlení (matematicky, nahoru, dolů)
  - Možnost určit typ generovaného dokladu (faktura || proforma)
  003
  - Ošetření notice a deprecated funkcí pro PHP 5.4
  004
  - Přechod na aktuální verzi TCPDF
  - Output option - nové možnosti uložení dokladu
  - Přidání nových dokladů - opravný daňový doklad/dobropis, storno faktura
  - Úprava zaokrouhlování - pokud se nezaokrouhluje DPH, tak se zaokrouhlení rozpouští do sazeb DPH
  - Možnost generovat čárový kód faktury
 * Verze 2.2
  001
  - Zrušení možnosti zaokrouhlovat DPH
  - Předělání systému zaokrouhlování - pouze jedna položka zaokrouhlení pro všechny sazby DPH
  - Možnost vypnutí počítání zaokrouhlení jako položky faktury
  - Možnost určení do které sazby rozpouštět položku zaokrouhlení
  - Podpora více sazeb DPH
  - Reverse charge
  002
  - Možnost zadat celé číslo účtu (včetně /kód banky) zadat přes SetCislouctu. Diky tomu je mozne prejmenovat kod banky v jazykovych prekladech a pouzivat toto pole pro druhe cislo uctu v plnem formatu
  - Možnost nastavení fontu
  003
  - Možnost rozpouštět zaokrouhlení do nulové sazby
  - Možnost vložení dalších informací do sekce "platební údaje"
  004
  - Možnost vypnutí zobrazení počtu položek
  - Metoda pro vracení celkové částky vypočítané třídou
  005
  - Možnost zadávání částek včetně DPH (výpočet dle § 37 odst. 2 zákona o DPH)
  - Příprava pro EET - nová metoda, která vrátí rozpočítané částky (shrnutí DPH) před generováním a následně možnost zadání FIK a dalších EET údajů
  - Oprava bugu generování QR kódu s nulovou či zápornou částkou
 * Verze 2.3
  001
  - TCPDF přes composer (pro možnost jednodušší aktualizace)
  - Možnost orámování hlavních sekcí (pro ekonomický tisk)
  - Namespaces
  - Možnost volby zda má být poznámka u položek vypsána nad, nebo pod názvem položky
  - Možnost generování více dokladů do jednoho PDF
  - Custom callback
  - Příklady použití
  002
  - kompatibilita s PHP 8
 * Verze 2.4
  001
  - kompatibilita s PHP 8.3
  - Možnost určení konkrétních sloupců s množstevními jednotkami
  - Refactoring
  - Doplněn PHPDoc
  - Doplněny datové a návratové typy
 002
  - možnost zvýrazněného nápisu "již uhrazeno - neplatit"
  - QR platba je zobrazena pouze pokud faktura ještě není uhrazena

 * ******************************************************************************************************
 * ***************************************************************************************************** */

namespace WFPfaktury;

require(dirname(__FILE__) . '/vendor/autoload.php');

class WFPfaktury extends \TCPDF {


    public WFPf_adresa $dodavatel;

    public WFPf_adresa $odberatel;

    public WFPf_adresa $konecnyPrijemce;

    public WFPf_informace $informace;

    public WFPf_platebniUdaje $platebniUdaje;

    public WFPf_nastaveni $nastaveni;

    public WFPf_email $email;

    public $customCallback;

    /**
     * @var WFPf_polozka[]
     */
    private array $polozky = [];
    private int $pocetPolozek = 0;
    private float $cenaCelkem = 0;
    private float $konstantaY = 0;
    private float $vPodpis = 0;
    private float $slevaCelkem = 0;
    private float $YPlatebniUdaje;
    private int $invoiceNo = 0;
    private int $invoicePageNo = 0;

    public function __construct() {
        parent::__construct();

        $this->nastaveni = new WFPf_nastaveni();
        $this->dodavatel = new WFPf_adresa();
        $this->odberatel = new WFPf_adresa();
        $this->konecnyPrijemce = new WFPf_adresa();
        $this->informace = new WFPf_informace();
        $this->email = new WFPf_email();
        $this->platebniUdaje = new WFPf_platebniUdaje();

        //set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        //$this->setLanguageArray($l);
        // ---------------------------------------------------------
        // set default font subsetting mode
        $this->setFontSubsetting(true);
        //$this->setFontSubsetting(false);
        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $this->SetFont($this->nastaveni->font, '', 14, '', true);

        $this->Open();

        //Nastaví tvůrce dokumentu (většinou název aplikace)
        $this->SetCreator("Created by WFPfaktury");

        //Nastaveni konstanty pro Y prirustek
        $this->konstantaY = 5;
    }

    /**
     * Do dashed line on defined points.
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $nb
     * @return void
     */
    private function DashedLine(float $x1, float $y1, float $x2, float $nb = 15) {
        $Pointilles = (abs($x1 - $x2) / $nb) / 2;

        for ($i = $x1; $i <= $x2; $i += $Pointilles + $Pointilles) {
            $this->Line($i, $y1, $i + 1, $y1); // upper dashes
        }
    }

    /**
     * Render PDF footer
     * @return void
     */
    public function Footer() {
        $this->SetFont($this->nastaveni->font, '', 8);
        $this->SetDrawColor(180, 180, 180);
        $this->SetTextColor(180, 180, 180);
        $this->SetLineWidth(0.1);
        $this->Line(10, 287, 200, 287);
        $this->SetXY(15, -10);
        $this->Cell(0, 8, WFPf_preklad::t('generovano_paticka'));
        $this->SetXY(0, -10);
        $this->Cell(210, 8, 'faktury.chci-www.cz', 0, 0, 'C');
        $this->SetXY(180, -10);
        $this->Cell(20, 8, WFPf_preklad::t('strana') . ' ' . $this->getPageNumGroupAlias() . '/' . $this->getPageGroupAlias(), 0, 0, 'L');
        $this->SetTextColor(0, 0, 0);
    }

    /**
     * Render items table heading
     * @param float $odsazeni
     * @return int
     */
    private function zahlavi(float $odsazeni) {
        $return = 0;
        if ($this->invoicePageNo != $this->PageNo()) {
            $this->SetFillColor($this->nastaveni->style['fillColor'][0], $this->nastaveni->style['fillColor'][1], $this->nastaveni->style['fillColor'][2]);
            $this->SetTextColor($this->nastaveni->style['fontColor'][0], $this->nastaveni->style['fontColor'][1], $this->nastaveni->style['fontColor'][2]);
            $this->Rect(10, $this->konstantaY + $odsazeni - 10, 190, 10, 'F', $this->nastaveni->getBorders());

            $this->SetFont($this->nastaveni->font, 'B', 10);
            $this->SetXY(15, $odsazeni - 10 + $this->konstantaY);
            $this->Cell(0, 10, WFPf_preklad::t('polozky_k_uhrade'));
            $this->SetTextColor(0, 0, 0);
            $return = 9;
            $odsazeni += 9;
        }

        if ($this->nastaveni->platceDPH) {
            $this->SetFont($this->nastaveni->font, 'B', 7);
            $this->SetXY(15, $odsazeni - 9 + $this->konstantaY);
            $this->Cell(0, 10, WFPf_preklad::t('nazev_polozky'));

            if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                $this->SetX(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu());
                $sirkaSloupce = 0;
                if ($this->nastaveni->zobrazeneSloupce['pocetmj']) {
                    $this->Cell(17, 10, WFPf_preklad::t('pocet_mj'), 0, 0, 'R');
                    $sirkaSloupce += 17;
                }
                if ($this->nastaveni->zobrazeneSloupce['mj']) {
                    $this->SetX(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu() + $sirkaSloupce);
                    $this->Cell(8, 10, WFPf_preklad::t('m_j'), 0,0, 'L');
                    $sirkaSloupce += 8;
                }
                if ($this->nastaveni->zobrazeneSloupce['cenamj']) {
                    $this->SetX(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu() + $sirkaSloupce);
                    $this->Cell(18, 10, WFPf_preklad::t('cena_za_m_j'), 0, 0, 'R');
                }
            }

            $this->SetX(114);
            $this->Cell(11, 10, WFPf_preklad::t('dph_procent'), 0, 0, 'R');
            $this->SetX(126);
            $this->Cell(23, 10, WFPf_preklad::t('bez_dph'), 0, 0, 'R');
            $this->SetX(152);
            $this->Cell(19, 10, WFPf_preklad::t('dph'), 0, 0, 'R');
            $this->SetX(172);
            $this->Cell(23, 10, WFPf_preklad::t('s_dph'), 0, 0, 'R');
        } else {
            // neplatce DPH
            $this->SetFont($this->nastaveni->font, 'B', 9);
            $this->SetXY(15, $odsazeni - 9 + $this->konstantaY);
            $this->Cell(0, 10, WFPf_preklad::t('nazev_polozky'));
            if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                $this->SetX(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false));
                $sirkaSloupce = 0;
                if ($this->nastaveni->zobrazeneSloupce['pocetmj']) {
                    $this->Cell(20, 10, WFPf_preklad::t('pocet_mj'), 0, 0, 'R');
                    $sirkaSloupce += 25;
                }
                if ($this->nastaveni->zobrazeneSloupce['mj']) {
                    $this->SetX(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce);
                    $this->Cell(15, 10, WFPf_preklad::t('m_j'));
                    $sirkaSloupce += 15;
                }
                if ($this->nastaveni->zobrazeneSloupce['cenamj']) {
                    $this->SetX(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce);
                    $this->Cell(25, 10, WFPf_preklad::t('cena_za_m_j'), 0, 0, 'R');
                }
            }

            $this->SetX(170);
            $this->Cell(25, 10, WFPf_preklad::t('cena_celkem'), 0, 0, 'R');
        }

        // sede cary
        $this->SetDrawColor(153, 153, 153);
        $this->Line(15, $odsazeni + $this->konstantaY, 195, $odsazeni + $this->konstantaY);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFont($this->nastaveni->font, '', 9);
        return $return;
    }

    /**
     * Vykreslí záhlaví faktury
     * @return void
     */
    public function Header() {
        $this->SetDrawColor(0, 0, 0);
        $this->SetFont($this->nastaveni->font, '', 12);

        $this->SetXY(10, 10);

        if (!empty($this->nastaveni->nazevDokladu)) {
            $this->Cell(187, 10, $this->nastaveni->nazevDokladu, '', 0, 'R');
        } else {
            switch ($this->nastaveni->typDokladu) {
                case 1:
                    // klasicka faktura
                    if ($this->nastaveni->platceDPH) {
                        $this->Cell(187, 10, WFPf_preklad::t('faktura_nadpis_platce') . $this->nastaveni->cislo_faktury, '', 0, 'R');
                    } else {
                        $this->Cell(187, 10, WFPf_preklad::t('faktura_nadpis_neplatce') . $this->nastaveni->cislo_faktury, '', 0, 'R');
                    }
                    break;
                case 2:
                    // proforma
                    $this->Cell(187, 10, WFPf_preklad::t('proforma_nadpis') . $this->nastaveni->cislo_faktury, '', 0, 'R');
                    break;
                case 3:
                    // ODD/dobropis
                    if ($this->nastaveni->platceDPH) {
                        $this->Cell(187, 10, WFPf_preklad::t('odd_nadpis') . $this->nastaveni->cislo_faktury, '', 0, 'R');
                    } else {
                        $this->Cell(187, 10, WFPf_preklad::t('dobropis_nadpis') . $this->nastaveni->cislo_faktury, '', 0, 'R');
                    }
                    break;
                case 4:
                    // storno
                    if ($this->nastaveni->platceDPH) {
                        $this->Cell(187, 10, WFPf_preklad::t('storno_nadpis_platce') . $this->nastaveni->cislo_faktury, '', 0, 'R');
                    } else {
                        $this->Cell(187, 10, WFPf_preklad::t('storno_nadpis') . $this->nastaveni->cislo_faktury, '', 0, 'R');
                    }
                    break;
            }
        }
        if (count($this->nastaveni->obrazek) > 0) {
            foreach ($this->nastaveni->obrazek as $obrazek) {
                if (isset($obrazek[0])) {
                    if (trim($obrazek[0]) != '') {
                        if ($obrazek[3] == 'A') {
                            $this->Image($obrazek[0], $obrazek[1], $obrazek[2], $obrazek[4], $obrazek[5]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Generate PDF invoice
     * @param bool $output - true for direct generating PDF in this method, false for generate PDF by output method
     * @return mixed
     */
    public function generuj($output = true) {
        $this->startPageGroup();
        $this->invoiceNo++;
        // pokud neni nastaven zadny jazyk, tak nacteme cestinu
        if (!WFPf_preklad::jeJazyk()) {
            $this->nastaveni->SetJazyk('cs');
        }
        $this->dodavatel->preklad();
        $this->odberatel->preklad();
        $this->konecnyPrijemce->preklad();

        $this->informace->preklad();
        $this->platebniUdaje->preklad();

        if (($this->nastaveni->sleva[0] > 0 || $this->nastaveni->sleva[3] == 1) && ($this->nastaveni->sleva[2] == 0 || $this->nastaveni->sleva[2] == 2) && $this->nastaveni->sleva[1] == 0) {
            $polozka = new WFPf_polozka('Sleva', 99, $this->nastaveni->sleva[0], 0, $this->nastaveni->sleva[4], true);
            if ($this->polozky[] = $polozka) {
                $this->pocetPolozek++;
            }
            //var $sleva = array(0,0,1,0); // sleva - prvni cislo udava hodnotu slevy, druhe udava zda je to sleva v korunach (0) nebo procentualni sleva (1), treti udava kde vypsat slevu 0 = mezi polozkami, 1=dole u celkove ceny, 2=polozky i dole, ctvrty parametr udava zda ma byt vypsana sleva i kdyz je nulova
        }

        if ($this->nastaveni->zaokrouhlovat == 0) {
            // zaokrouhleni pro neplatce DPH
            $polozka = new WFPf_polozka(WFPf_preklad::t('zaokrouhleni'), 1, 0, 0, 0, true);
            if ($this->polozky[] = $polozka) {
                $this->pocetPolozek++;
            }
        }

        if ($this->nastaveni->konecnyPrijemce['vypisovat'] && !$this->nastaveni->konecnyPrijemce['jina_adresa']) {
            // nastavime adresu konecneho prijemce na stejnou jako ma odberatel
            $this->konecnyPrijemce = $this->odberatel;
        }


        $this->AddPage();
        $this->invoicePageNo = $this->PageNo();
        // el. podpis
        if ($this->nastaveni->podpis != '') {
            $this->setSignature($this->nastaveni->podpis, $this->nastaveni->podpis, $this->nastaveni->podpis_heslo, '', 2, $this->nastaveni->podpis_info);
        }

        //Nastaví autora dokumentu.
        $this->SetAuthor($this->nastaveni->autor);

        //Titulek dokumentu
        $this->SetTitle($this->nastaveni->titulek);

        $this->SetAutoPageBreak(false, 0.5);
        // logo v zahlavi
        if (count($this->nastaveni->obrazek) > 0) {
            foreach ($this->nastaveni->obrazek as $obrazek) {
                if (isset($obrazek[0])) {
                    if (trim($obrazek[0]) != '') {
                        if ($obrazek[3] == 'F') {
                            $this->Image($obrazek[0], $obrazek[1], $obrazek[2], $obrazek[4], $obrazek[5]);
                        }
                    }
                }
            }
        }

        $this->SetFillColor($this->nastaveni->style['fillColor'][0], $this->nastaveni->style['fillColor'][1], $this->nastaveni->style['fillColor'][2]);
        $this->SetTextColor($this->nastaveni->style['fontColor'][0], $this->nastaveni->style['fontColor'][1], $this->nastaveni->style['fontColor'][2]);
        $this->Rect(10, 20, 190, 10, 'F', $this->nastaveni->getBorders());

        // vypsat carovy kod faktury
        if ($this->nastaveni->carovyKod['kod'] > 0) {
            $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => 1, 'vpadding' => 0, 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
                'text' => false, 'font' => $this->nastaveni->font, 'fontsize' => 5.2, 'stretchtext' => 0);
            $this->write1DBarcode($this->nastaveni->carovyKod['kod'], 'c128', $this->nastaveni->carovyKod['x'], $this->nastaveni->carovyKod['y'], ($this->nastaveni->carovyKod['sirka'] == 0 ? '' : $this->nastaveni->carovyKod['sirka']), ($this->nastaveni->carovyKod['vyska'] == 0 ? '' : $this->nastaveni->carovyKod['vyska']), 0.4, $style, 'T');
        }

        $this->SetFont($this->nastaveni->font, 'B', 10);
        $this->SetXY(15, 20);
        $this->Cell(0, 10, WFPf_preklad::t('dodavatel'));
        $this->SetTextColor(0, 0, 0);
        $this->vypisAdresu($this->dodavatel, 15, 33, 9, true);

        $this->SetTextColor($this->nastaveni->style['fontColor'][0], $this->nastaveni->style['fontColor'][1], $this->nastaveni->style['fontColor'][2]);
        $this->SetFont($this->nastaveni->font, 'B', 10);
        $this->SetXY(113, 20);
        $this->Cell(0, 10, WFPf_preklad::t('odberatel'));
        $this->SetTextColor(0, 0, 0);
        $this->vypisAdresu($this->odberatel, 113, 33, 9, true);

        if ($this->nastaveni->konecnyPrijemce['vypisovat']) {
            $this->SetFillColor($this->nastaveni->style['fillColor'][0], $this->nastaveni->style['fillColor'][1], $this->nastaveni->style['fillColor'][2]);
            $this->SetTextColor($this->nastaveni->style['fontColor'][0], $this->nastaveni->style['fontColor'][1], $this->nastaveni->style['fontColor'][2]);
            $this->Rect(112, $this->getY() + 3, 87, 10, 'F', $this->nastaveni->getBorders());
            $this->SetFont($this->nastaveni->font, 'B', 10);
            $this->SetXY(113, $this->getY() + 3);
            $this->Cell(0, 10, WFPf_preklad::t('konecnyPrijemce'));
            $this->SetTextColor(0, 0, 0);
            $this->vypisAdresu($this->konecnyPrijemce, 113, $this->getY() + 13, 9, true);
        }

        $this->konstantaY = $this->konstantaY - 62;


        // nakresleni sedych car
        // pole pro "platebni udaje"
        $this->SetFillColor($this->nastaveni->style['fillColor'][0], $this->nastaveni->style['fillColor'][1], $this->nastaveni->style['fillColor'][2]);
        $this->Rect(10, $this->konstantaY + 65, 190, 10, 'F', $this->nastaveni->getBorders());

        /* $this->SetFont($this->nastaveni->font, 'B', 10);
          $this->SetXY(15, 65 + $this->konstantaY);
          $this->Cell(0, 10, 'Informace o faktuře'); */

        // vypis informaci o fakture
        $druhy_sloupec = $this->vypisArrayCell($this->platebniUdaje, 15, 78 + $this->konstantaY, 9, 37);
        $this->SetFont($this->nastaveni->font, 'B', 10);
        $this->SetXY(15, 65 + $this->konstantaY);
        $this->SetTextColor($this->nastaveni->style['fontColor'][0], $this->nastaveni->style['fontColor'][1], $this->nastaveni->style['fontColor'][2]);
        $this->Cell(0, 10, WFPf_preklad::t('platebni_udaje'));
        $this->SetTextColor(0, 0, 0);
        $prvni_sloupec = $this->vypisArrayCell($this->informace, 113, 78 + $this->konstantaY, 9, 50);

        if ($prvni_sloupec > $druhy_sloupec) {
            $vetsi_sloupec = $prvni_sloupec;
        } else {
            $vetsi_sloupec = $druhy_sloupec;
        }

        $this->YPlatebniUdaje = $this->konstantaY + 77;

        $jizUhrazeno = false;
        if ($this->nastaveni->zalohy > 0) {
            if (($this->spocitejCenuCelkem() - $this->nastaveni->zalohy) < 0.5) {
                $jizUhrazeno = true;
            }
        }

        // je tam dost mista pro QR kod?
        if ($this->nastaveni->qr_platba['vypisovat'] && $this->nastaveni->qr_platba['strana'] == 'PU' && !$jizUhrazeno) {
            if ($vetsi_sloupec < $this->nastaveni->qr_platba['velikost'] + 6) {
                $vetsi_sloupec = $this->nastaveni->qr_platba['velikost'] + 6;
            }
        }

        $this->konstantaY += $vetsi_sloupec;

        if ($this->nastaveni->jizUhrazenoVPlatebnichUdajich && $jizUhrazeno) {
            $this->SetXY(15, $this->konstantaY + 76);
            $this->SetFont($this->nastaveni->font, 'B', 20);
            $this->Cell(180, 10, WFPf_preklad::t('jiz_uhrazeno_neplatit'), 0, 0, 'C');
            $this->konstantaY += 15;
        }

        // seda cara
        $this->SetFillColor($this->nastaveni->style['fillColor'][0], $this->nastaveni->style['fillColor'][1], $this->nastaveni->style['fillColor'][2]);
        $this->SetTextColor($this->nastaveni->style['fontColor'][0], $this->nastaveni->style['fontColor'][1], $this->nastaveni->style['fontColor'][2]);
        $this->Rect(10, $this->konstantaY + 76, 190, 10, 'F', $this->nastaveni->getBorders());

        $this->SetFont($this->nastaveni->font, 'B', 10);
        $this->SetXY(15, 76 + $this->konstantaY);
        $this->Cell(0, 10, WFPf_preklad::t('polozky_k_uhrade'));
        $this->SetTextColor(0, 0, 0);

        $this->zahlavi(95);
        $this->vypisPolozky();

        if (isset($this->nastaveni->text_konec[0]) && $this->nastaveni->text_konec[0] != '') {
            $this->SetXY(15, -16);
            $this->SetFont($this->nastaveni->font, '' . $this->nastaveni->text_konec[2] . '', $this->nastaveni->text_konec[1]);
            $this->Cell(80, $this->nastaveni->text_konec[1] - 4, $this->nastaveni->text_konec[0], 0, 0, 'L');
        }

        if (count($this->nastaveni->obrazek) > 0) {
            foreach ($this->nastaveni->obrazek as $obrazek) {
                if (isset($obrazek[0])) {
                    if (trim($obrazek[0]) != '') {
                        if (substr($obrazek[2], 0, 1) != 'c') {
                            $vertik = $obrazek[2];
                        } else {
                            $vertik = $this->vPodpis;
                            if (substr($obrazek[2], 1, 1) == '+') {
                                $vertik += substr($obrazek[2], 2);
                            } else {
                                $vertik = $vertik - substr($obrazek[2], 2);
                            }
                        }

                        if ($obrazek[3] == 'L') {
                            $this->Image($obrazek[0], $obrazek[1], $vertik, $obrazek[4], $obrazek[5]);
                        }
                    }
                }
            }
        }

        // QR Platba, pokud ma byt jen na posledni strane
        if (!$jizUhrazeno && $this->nastaveni->qr_platba['vypisovat'] && $this->nastaveni->qr_platba['strana'] == 'L') {
            if (substr($this->nastaveni->qr_platba['y'], 0, 1) != 'c') {
                $vertik = $this->nastaveni->qr_platba['y'];
            } else {
                $vertik = $this->vPodpis;
                if (substr($this->nastaveni->qr_platba['y'], 1, 1) == '+') {
                    $vertik += substr($this->nastaveni->qr_platba['y'], 2);
                } else {
                    $vertik = $vertik - substr($this->nastaveni->qr_platba['y'], 2);
                }
            }
            $this->RenderQRPlatba($this->nastaveni->qr_platba['x'], $vertik);
        }

        $this->SetDisplayMode('real');

        // QR Platba, pokud ma byt jen na prvni strane
        if (!$jizUhrazeno && $this->nastaveni->qr_platba['vypisovat'] && ($this->nastaveni->qr_platba['strana'] == 'F' || $this->nastaveni->qr_platba['strana'] == 'PU')) {
            $this->SetPage(1);
            $this->RenderQRPlatba($this->nastaveni->qr_platba['x'], $this->nastaveni->qr_platba['strana'] == 'PU' ? $this->YPlatebniUdaje + 1 : $this->nastaveni->qr_platba['y']);
        }

        if (is_callable($this->customCallback)) {
            $c = $this->customCallback;
            $c($this);
        }

        if ($output) {
            $out = $this->outputPDF();
        } else {
            $out = true;
        }
        return $out;
    }

    /**
     * Output generated invoice to PDF and send to email if is enabled
     * @return mixed
     */
    public function outputPDF(): mixed {
        if ($this->nastaveni->send_to_mail) {
            // Odesleme soubor na emailovou adresu
            $send_mail = true;
            if (!empty($this->email->phpMailerPath)) {
                require_once $this->email->phpMailerPath;
            }
            $reg_email = "/^([a-zA-Z0-9_.-]+@([a-zA-Z0-9_-]+\.)+[a-z]{2,4}){1}$/i";

            if (preg_match($reg_email, $this->email->address)) {
                // pokud je zadana adresa validni tak odesleme mail
                $mail = new phpmailer();

                $mail->From = $this->email->from;
                $mail->FromName = $this->email->fromName;
                $mail->Subject = $this->email->subject;
                $mail->Body = $this->email->body;
                $mail->AddAddress($this->email->address);
                $jmeno_souboru = $this->nastaveni->jmenosouboru;
            }
        } else {
            $send_mail = false;
        }


        if ($this->nastaveni->outputType == 'F' || $this->nastaveni->outputType == 'FI') {
            $out = $this->Output($this->nastaveni->jmenosouboru, $this->nastaveni->outputType);

            if ($send_mail) {
                $mail->AddAttachment($jmeno_souboru, "faktura.pdf");
                $mail->Send();
            }
        } else {
            $out = $this->Output($this->nastaveni->jmenosouboru, $this->nastaveni->outputType);
        }
        return $out;
    }

    /**
     * Vyčistí data předchozí faktury (adresy odběratele, koncového příjemce, položky faktury, platební informace)
     * @return self
     */
    public function clearData(): self {
        $this->odberatel = new WFPf_adresa();
        $this->konecnyPrijemce = new WFPf_adresa();
        $this->informace = new WFPf_informace();
        $this->platebniUdaje = new WFPf_platebniUdaje();
        $this->polozky = array();
        $this->nastaveni->clear();
        $this->pocetPolozek = 0;
        $this->cenaCelkem = $this->slevaCelkem = 0;

        //Nastaveni konstanty pro Y prirustek
        $this->konstantaY = 5;
        return $this;
    }

    /**
     * Vrati celkovou cenu, nutno volat az po vygenerovani faktury
     * @return float Celkova cena
     */
    public function celkovaCena(): float {
        return $this->cenaCelkem;
    }

    /**
     * Přidá položku faktury,
     * @param string $nazev
     * @param float $mnozstvi def 1
     * @param float $cena def 0
     * @param string $mj def ''
     * @param float $dph def 0
     * @param string $poznamka def ''
     * @param int|null $ean def null
     * @return bool
     */
    public function pridejPolozku(string $nazev, float $mnozstvi = 1, float $cena = 0, string $mj = '', float $dph = 0, string $poznamka = '', ?int $ean = null): bool {
        if (trim($nazev) != '') {

            $polozka = new WFPf_polozka($nazev, $mnozstvi, $cena, $mj, $dph, false, $ean, $poznamka);
            if (is_a($polozka, "WFPfaktury\WFPf_polozka")) {

                if ($this->polozky[] = $polozka) {
                    $this->pocetPolozek++;
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function spocitejCenuCelkem(): float {
        $polozky = $this->polozky;
        $slevaCelkem = 0;
        $cenaCelkem = 0;
        if ($this->nastaveni->platceDPH) {
            $zakladDphCelkem = array();
            $dphCelkem = array();
            foreach ($this->nastaveni->sazbyDPH as $k => $v) {
                $zakladDphCelkem[$k] = 0;
                $dphCelkem[$k] = 0;
            }
        }
        foreach ($polozky as $objekty) {
            if ($this->nastaveni->sleva[0] > 0 && $this->nastaveni->sleva[1] == 1) {
                $objekty->cena = $objekty->cena * ((100 - $this->nastaveni->sleva[0]) / 100);
            }
            if ($this->nastaveni->platceDPH) {
                if ($objekty->special) {
                    if ($objekty->mnozstvi == 99) {
                        $slevaCelkem = -$objekty->cena;

                        $cenaCelkem += $slevaCelkem;
                        $zakladDphCelkem[(string)$objekty->dph] += ($slevaCelkem);
                    } else {
                        switch ($this->nastaveni->zpusobZaokrouhleni) {
                            case 2:
                                $zaokrouhleno = ceil($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            case 3:
                                $zaokrouhleno = floor($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            default:
                                $zaokrouhleno = round($this->nastaveni->zaokrouhlit * ($cenaCelkem), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit;
                                break;
                        }

                        $rozdilSDPH = $zaokrouhleno - $cenaCelkem;
                        $cenaCelkem += $rozdilSDPH;
//                        $pouziteSazby = array();
//                        $maxSazba = 0;
//                        $maxSazbaSum = 0;
//                        foreach ($zakladDphCelkem as $kSazba => $castka) {
//                            if ($castka != 0) {
//                                $pouziteSazby[] = (float)$kSazba;
//                            }
//
//                            if ($castka > $maxSazbaSum) {
//                                $maxSazba = (float)$kSazba;
//                                $maxSazbaSum = $castka;
//                            }
//                        }
//                        $sazbaZaokrouhleni = 0;
//
//                        // zjistime do ktere sazby rozpocitame zaokrouhleni
//                        switch ($this->nastaveni->zaokrouhleniRozpusteni) {
//                            case 1:
//                                // do nejvyssi pouzite sazby
//                                $sazbaZaokrouhleni = max($pouziteSazby);
//                                break;
//                            case 2:
//                                // do nejnizsi pouzite sazby
//                                $sazbaZaokrouhleni = min($pouziteSazby);
//                                break;
//                            case 3:
//                                // do sazby s nejvyssi celkovou castkou
//                                $sazbaZaokrouhleni = $maxSazba;
//                                break;
//                        }
//                        $zakladZaokrouhleni = (100 / (100 + $sazbaZaokrouhleni)) * $rozdilSDPH;
//                        if ($this->nastaveni->typZaokrouhleni == 1) {
//                            // pocitame s nezaokrouhlenymi
//                            $zaokrouhleniDPH = $rozdilSDPH - $zakladZaokrouhleni;
//                        } else {
//                            // pociteme se zaokrouhlenymi
//                            $zaokrouhleniDPH = $rozdilSDPH - round($zakladZaokrouhleni, 2);
//                        }
//
//                        if (!$this->nastaveni->reverseCharge) {
//                            $dphCelkem[(string)$sazbaZaokrouhleni] += round($zaokrouhleniDPH, 2);
//                            $zakladDphCelkem[(string)$sazbaZaokrouhleni] += round($zakladZaokrouhleni, 2);
//                        } else {
//                            $zaokrouhleniDPH = 0;
//                            $zakladZaokrouhleni = $rozdilSDPH;
//                            $zakladDphCelkem[(string)$sazbaZaokrouhleni] += round($rozdilSDPH, 2);
//                        }
                    }
                } else {
                    $cenaZaklad = $objekty->cena;
                    if ($this->nastaveni->castkySDPH) {
                        $cenaZaklad = $objekty->cena - ($objekty->cena * round(1 - (100 / (100 + $objekty->dph)), 4));
                    }
                    if (!$this->nastaveni->reverseCharge) {
                        $dphcko = $objekty->cena * $objekty->mnozstvi * $objekty->dph / 100;
                        if ($this->nastaveni->castkySDPH) {
                            $dphcko = $objekty->mnozstvi * ($objekty->cena * round(1 - (100 / (100 + $objekty->dph)), 4));
                        }
                    } else {
                        $dphcko = 0;
                    }
                    $cenaPolozka = $cenaZaklad * $objekty->mnozstvi + $dphcko;
//                    if ($this->nastaveni->typZaokrouhleni == 1) {
//                        $dphCelkem[(string)$objekty->dph] += $dphcko;
//                        $zakladDphCelkem[(string)$objekty->dph] += ($cenaZaklad * $objekty->mnozstvi);
//                    } else {
//                        $dphCelkem[(string)$objekty->dph] += round($dphcko, 2);
//                        $zakladDphCelkem[(string)$objekty->dph] += round($cenaZaklad * $objekty->mnozstvi, 2);
//                    }

                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $cenaCelkem += $cenaPolozka;
                    } else {
                        $cenaCelkem += round($cenaPolozka, 2);
                    }
                }
            } else {
                if ($objekty->special) {
                    if ($objekty->mnozstvi == 99) {
                        $slevaCelkem = -$objekty->cena;

                        if ($this->nastaveni->typZaokrouhleni == 1) {
                            $cenaCelkem += $slevaCelkem;
                        } else {
                            $cenaCelkem += round($slevaCelkem, 2);
                        }
                    } else {
                        switch ($this->nastaveni->zpusobZaokrouhleni) {
                            case 2:
                                $zaokrouhleno = ceil($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            case 3:
                                $zaokrouhleno = floor($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            default:
                                $zaokrouhleno = round($this->nastaveni->zaokrouhlit * ($cenaCelkem), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit;
                                break;
                        }

                        $rozdil = $zaokrouhleno - $cenaCelkem;
                        $cenaCelkem += $rozdil;
                    }
                } else {
                    // normalni polozka
                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $cenaCelkem += ($objekty->cena * $objekty->mnozstvi);
                    } else {
                        $cenaCelkem += round($objekty->cena * $objekty->mnozstvi, 2);
                    }
                }
            }
        }
        return $cenaCelkem;
    }

    private function vypisPolozky() {
        if ($this->nastaveni->platceDPH) {
            $this->SetFont($this->nastaveni->font, '', 8);
            $zakladDphCelkem = array();
            $dphCelkem = array();
            foreach ($this->nastaveni->sazbyDPH as $k => $v) {
                $zakladDphCelkem[$k] = 0;
                $dphCelkem[$k] = 0;
            }
        } else {
            $this->SetFont($this->nastaveni->font, '', 9);
        }
        $y = 96 + $this->konstantaY;
        $this->SetXY(15, $y);
        $this->SetFillColor(204, 204, 204);
        $i = 1;

        // spocitame vysku paticky
        $this->startTransaction();
        $patickaY = $this->getY();
        $this->paticka();
        $vyskaPaticky = $this->getY() - $patickaY;
        $this->rollbackTransaction(true);

        // konstanta pro preskoceni na dalsi stranku: vyska stranky - vyska paticky - pole pro shrnuti DPH aspol.

        $konstanta = 0;
        // mame vypsat slevu ?
        $vslev = false;
        if (($this->nastaveni->sleva[0] > 0 || $this->nastaveni->sleva[3] == 1) && $this->nastaveni->sleva[1] == 1) {
            $vslev = true;
        }
        if (($this->nastaveni->sleva[0] > 0 || $this->nastaveni->sleva[3] == 1) && $this->nastaveni->sleva[1] == 0 && $this->nastaveni->sleva[2] > 0) {
            $vslev = true;
        }


        if ($this->nastaveni->platceDPH && $this->nastaveni->shrnutiDPH) {
            $konstanta -= 7;
        } else {
            if (!$vslev) {
                $konstanta += 7;
            }
        }
        $zalomeniStrankyPolozky = 297 - 15 - 40 - $vyskaPaticky + $konstanta;
        $zalomeniStrankyKonec = 297 - 15;
        $cisloPolozky = 0;

        // styl pro EAN u polozek
        $style = array('position' => '', 'align' => 'C', 'stretch' => false, 'fitwidth' => true, 'cellfitalign' => '', 'border' => false, 'hpadding' => 1, 'vpadding' => 4, 'fgcolor' => array(0, 0, 0), 'bgcolor' => false, //array(255,255,255),
            'text' => true, 'font' => $this->nastaveni->font, 'fontsize' => 5.2, 'stretchtext' => 0);

        $ye = 0;
        $zaokrouhleno = false;
        foreach ($this->polozky as $objekty) {

            $cisloPolozky++;
            if (($cisloPolozky + 1) >= $this->pocetPolozek) {
                $zalomeniStranky = $zalomeniStrankyPolozky;
            } else {
                $zalomeniStranky = $zalomeniStrankyKonec;
            }
            $this->setCellPaddings('', 0.75, '', 0.75);
            //$hh = $this->getStringHeight(55, $objekty->nazev);
            //$htest = $hh + $y;
            if ($this->GetY() > $zalomeniStranky || ($this->getStringHeight(55, $objekty->nazev) + $y) >= $zalomeniStranky) {
                $this->AddPage();
                $this->konstantaY = 0;
                $y = 32 + $this->zahlavi(30);
                if ($this->nastaveni->platceDPH) {
                    $this->SetFont($this->nastaveni->font, '', 8);
                } else {
                    $this->SetFont($this->nastaveni->font, '', 9);
                }
            }

            if (fmod($i, 2) == 0) {
                if (is_array($this->nastaveni->style['itemFillColor'])) {
                    $this->SetFillColor($this->nastaveni->style['itemFillColor'][0], $this->nastaveni->style['itemFillColor'][1], $this->nastaveni->style['itemFillColor'][2]);
                    $this->SetTextColor($this->nastaveni->style['itemFontColor'][0], $this->nastaveni->style['itemFontColor'][1], $this->nastaveni->style['itemFontColor'][2]);
                    $vybarvit = true;
                } else {
                    $this->SetFillColor(255, 255, 255);
                    $this->SetTextColor(0, 0, 0);
                    $vybarvit = false;
                }
            } else {
                $this->SetFillColor(255, 255, 255);
                $this->SetTextColor(0, 0, 0);
                $vybarvit = false;
            }


            // pokud je procentualni sleva, tak ji uplatnime na kazdou polozku
            if ($this->nastaveni->sleva[0] > 0 && $this->nastaveni->sleva[1] == 1) {
                $objekty->cena = $objekty->cena * ((100 - $this->nastaveni->sleva[0]) / 100);
            }

            if ($this->nastaveni->platceDPH) {
                // platci DPH

                $y += 0.25 + $ye;
                $this->SetXY(15, $y);
                $poznamkaWidth = 100;
                if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                    $poznamkaWidth -= $this->nastaveni->getSirkaZobrazenychSloupcu() + 5;
                }
                $isEan = false;
                $eanY = 0;
                $this->startTransaction();
                //$this->MultiCell(70, 5, $objekty->nazev, 0, 'L');
                $ye = 0;
                $y1 = $this->GetY();
                if (!empty($objekty->ean)) {
                    if (is_numeric($objekty->ean) && (int)$objekty->ean > 0) {
                        $isEan = true;
                        $this->write1DBarcode($objekty->ean, 'c128', '', '', 30, 8, 0.4, $style, 'T');
                        //$ye = 1;
                        //$y2 += 1;
                        //$objekty->poznamka = 'lorem ipsum asdas ahs kasdh kh ask ksfdhf sdfh sdkfh askfh sdf lorem ipsum asdas ahs kasdh kh ask ksfdhf sdfh sdkfh askfh sdf lorem ipsum asdas ahs kasdh kh ask ksfdhf sdfh sdkfh askfh sdf ';
                        $poznamkaWidth -= $this->getX() - 15;
                        $eanY = $this->getY();
                    }
                }
                $x2 = $this->getX();
                $this->SetXY($x2, $y);

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'top') {
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    $this->setXY($x2, $this->GetY());
                }

                $this->MultiCell($poznamkaWidth, 5, $objekty->nazev, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'B');

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'bottom') {
                    $this->setXY($x2, $this->GetY());
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    if ($isEan) {
                        //$ye = 0;
                        //$y2 -= 1;
                    }
                }

                $y2 = $this->GetY();
                if ($y1 + 8 > $y2 && $isEan) {
                    $ye = 0;
                    $y2 += 3;
                }
                $this->rollbackTransaction(true);

                $yH = $y2 - $y1 + $ye;
                $this->setCellPaddings('', '', '', '');

                if ($vybarvit) {
                    $this->Rect(15, $y1 - 0.15, 180, $yH - 0.15 + $ye, 'F');
                }

                if (!empty($objekty->ean)) {
                    if (is_numeric($objekty->ean) && (int)$objekty->ean > 0) {
                        $this->write1DBarcode($objekty->ean, 'c128', '', '', 30, 8, 0.4, $style, 'T');
                    }
                }
                $x2 = $this->getX();
                $this->SetXY($x2, $y);

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'top') {
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    $this->setXY($x2, $this->GetY());
                }

                $this->MultiCell($poznamkaWidth, 5, $objekty->nazev, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'B');

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'bottom') {
                    $this->setXY($x2, $this->GetY());
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    if ($isEan) {
                        //$y2 -= 1;
                    }
                }

                if (is_array($this->nastaveni->podtrzeni) && $this->pocetPolozek > $i) {
                    $this->SetDrawColor($this->nastaveni->podtrzeni[0], $this->nastaveni->podtrzeni[1], $this->nastaveni->podtrzeni[2]);
                    $this->SetLineWidth(0.2);
                    $this->Line(15, $y2 + $ye, 195, $y2 + $ye);
                    $this->SetDrawColor(0, 0, 0);
                }

                if ($objekty->special) {
                    if ($objekty->mnozstvi == 99) {
                        /* if ($this->nastaveni->sleva[1]==1) $this->slevaCelkem = (-1)*$objekty->cena/100 * $this->cenaCelkem;
                          else $this->slevaCelkem = $objekty->cena; */
                        $this->slevaCelkem = -$objekty->cena;

                        $this->cenaCelkem += $this->slevaCelkem;
                        //$zakladDphCelkem[0] += $this->slevaCelkem;
                        $zakladDphCelkem[(string)$objekty->dph] += ($this->slevaCelkem);


                        $this->SetXY(126, $y);
                        $this->Cell(23, 5, number_format(($this->slevaCelkem), 2, ',', ' '), 0, 0, 'R');
                        $this->SetXY(152, $y);
                        $this->Cell(19, 5, number_format(0, 2, ',', ' '), 0, 0, 'R');
                        $this->SetXY(172, $y);
                        $this->Cell(23, 5, number_format(($this->slevaCelkem), 2, ',', ' '), 0, 0, 'R');
                    } else {
                        switch ($this->nastaveni->zpusobZaokrouhleni) {
                            case 2:
                                $zaokrouhleno = ceil($this->nastaveni->zaokrouhlit * ($this->cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            case 3:
                                $zaokrouhleno = floor($this->nastaveni->zaokrouhlit * ($this->cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            default:
                                $zaokrouhleno = round($this->nastaveni->zaokrouhlit * ($this->cenaCelkem), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit;
                                break;
                        }

                        $rozdilSDPH = $zaokrouhleno - $this->cenaCelkem;
                        $this->cenaCelkem += $rozdilSDPH;
                        $pouziteSazby = array();
                        $maxSazba = 0;
                        $maxSazbaSum = 0;
                        foreach ($zakladDphCelkem as $kSazba => $castka) {
                            if ($castka != 0) {
                                $pouziteSazby[] = (float)$kSazba;
                            }

                            if ($castka > $maxSazbaSum) {
                                $maxSazba = (float)$kSazba;
                                $maxSazbaSum = $castka;
                            }
                        }
                        $sazbaZaokrouhleni = 0;

                        // zjistime do ktere sazby rozpocitame zaokrouhleni
                        switch ($this->nastaveni->zaokrouhleniRozpusteni) {
                            case 1:
                                // do nejvyssi pouzite sazby
                                $sazbaZaokrouhleni = max($pouziteSazby);
                                break;
                            case 2:
                                // do nejnizsi pouzite sazby
                                $sazbaZaokrouhleni = min($pouziteSazby);
                                break;
                            case 3:
                                // do sazby s nejvyssi celkovou castkou
                                $sazbaZaokrouhleni = $maxSazba;
                                break;
                        }
                        $zakladZaokrouhleni = (100 / (100 + $sazbaZaokrouhleni)) * $rozdilSDPH;
                        if ($this->nastaveni->typZaokrouhleni == 1) {
                            // pocitame s nezaokrouhlenymi
                            $zaokrouhleniDPH = $rozdilSDPH - $zakladZaokrouhleni;
                        } else {
                            // pociteme se zaokrouhlenymi
                            $zaokrouhleniDPH = $rozdilSDPH - round($zakladZaokrouhleni, 2);
                        }

                        if (!$this->nastaveni->reverseCharge) {
                            $dphCelkem[(string)$sazbaZaokrouhleni] += round($zaokrouhleniDPH, 2);
                            $zakladDphCelkem[(string)$sazbaZaokrouhleni] += round($zakladZaokrouhleni, 2);
                        } else {
                            $zaokrouhleniDPH = 0;
                            $zakladZaokrouhleni = $rozdilSDPH;
                            $zakladDphCelkem[(string)$sazbaZaokrouhleni] += round($rozdilSDPH, 2);
                        }

                        if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                            $this->SetXY(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu(), $y);
                            $sirkaSloupce = 0;
                            if ($this->nastaveni->zobrazeneSloupce['pocetmj']) {
                                $this->Cell(17, 5, 1, 0, 0, 'R');
                                $sirkaSloupce += 17;
                            }
                            if ($this->nastaveni->zobrazeneSloupce['mj']) {
                                $this->SetXY(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu() + $sirkaSloupce, $y);
                                $this->Cell(0, 5, '');
                                $sirkaSloupce += 9;
                            }
                            if ($this->nastaveni->zobrazeneSloupce['cenamj']) {
                                $this->SetXY(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu() + $sirkaSloupce, $y);
                                $this->Cell(18, 5, number_format($zakladZaokrouhleni, 2, ',', ' '), 0, 0, 'R');
                            }
                        }

                        $this->SetXY(114, $y);
                        $this->Cell(11, 5, (string)$sazbaZaokrouhleni, 0, 0, 'R');

                        $this->SetXY(126, $y);
                        $this->Cell(23, 5, number_format(($zakladZaokrouhleni), 2, ',', ' '), 0, 0, 'R');
                        $this->SetXY(152, $y);
                        $this->Cell(19, 5, number_format($zaokrouhleniDPH, 2, ',', ' '), 0, 0, 'R');

                        $this->SetXY(172, $y);
                        $this->Cell(23, 5, number_format(round($rozdilSDPH, 2) == 0 ? 0 : $rozdilSDPH, 2, ',', ' '), 0, 0, 'R');

                        $zaokrouhleno = true;
                        if ($this->nastaveni->zaokrouhleniPolozka) {
                            $this->pocetPolozek--;
                        }
                    }
                } else {
                    $cenaZaklad = $objekty->cena;
                    if ($this->nastaveni->castkySDPH) {
                        $cenaZaklad = $objekty->cena - ($objekty->cena * round(1 - (100 / (100 + $objekty->dph)), 4));
                    }
                    if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                        $this->SetXY(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu(), $y);
                        $sirkaSloupce = 0;
                        if ($this->nastaveni->zobrazeneSloupce['pocetmj']) {
                            $this->Cell(17, 5, $objekty->mnozstvi, 0, 0, 'R');
                            $sirkaSloupce += 17;
                        }
                        if ($this->nastaveni->zobrazeneSloupce['mj']) {
                            $this->SetXY(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu() + $sirkaSloupce, $y);
                            $this->Cell(0, 5, $objekty->mj);
                            $sirkaSloupce += 9;
                        }
                        if ($this->nastaveni->zobrazeneSloupce['cenamj']) {
                            $this->SetXY(70 + 35 - $this->nastaveni->getSirkaZobrazenychSloupcu() + $sirkaSloupce, $y);
                            $this->Cell(18, 5, number_format($cenaZaklad, 2, ',', ' '), 0, 0, 'R');
                        }
                    }

                    $this->SetXY(114, $y);
                    $this->Cell(11, 5, $objekty->dph, 0, 0, 'R');
                    $this->SetXY(126, $y);
                    $this->Cell(23, 5, number_format(($cenaZaklad * $objekty->mnozstvi), 2, ',', ' '), 0, 0, 'R');
                    $this->SetXY(152, $y);
                    if (!$this->nastaveni->reverseCharge) {
                        $dphcko = $objekty->cena * $objekty->mnozstvi * $objekty->dph / 100;
                        if ($this->nastaveni->castkySDPH) {
                            $dphcko = $objekty->mnozstvi * ($objekty->cena * round(1 - (100 / (100 + $objekty->dph)), 4));
                        }
                    } else {
                        $dphcko = 0;
                    }
                    $cenaCelkem = $cenaZaklad * $objekty->mnozstvi + $dphcko;
                    $this->Cell(19, 5, number_format($dphcko, 2, ',', ' '), 0, 0, 'R');
                    $this->SetXY(172, $y);
                    $this->Cell(23, 5, number_format($cenaCelkem, 2, ',', ' '), 0, 0, 'R');

                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $dphCelkem[(string)$objekty->dph] += $dphcko;
                        $zakladDphCelkem[(string)$objekty->dph] += ($cenaZaklad * $objekty->mnozstvi);
                    } else {
                        $dphCelkem[(string)$objekty->dph] += round($dphcko, 2);
                        $zakladDphCelkem[(string)$objekty->dph] += round($cenaZaklad * $objekty->mnozstvi, 2);
                    }

                    $this->SetX(15);
                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $this->cenaCelkem += $cenaCelkem;
                    } else {
                        $this->cenaCelkem += round($cenaCelkem, 2);
                    }
                }
                $i++;
                $y = $y2 + $this->nastaveni->vzdalenost_polozek; //+ $plus;
            } else {
                // neplatci DPH

                $y += 0.25 + $ye;
                $this->SetXY(15, $y);
                $poznamkaWidth = 155;
                if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                    $poznamkaWidth -= $this->nastaveni->getSirkaZobrazenychSloupcu(false) + 5;
                }

                $isEan = false;


                $y1 = $this->GetY();
                $this->startTransaction();
                $ye = 0;
                if (!empty($objekty->ean)) {
                    if (is_numeric($objekty->ean) && (int)$objekty->ean > 0) {
                        $isEan = true;
                        $this->write1DBarcode($objekty->ean, 'c128', '', '', 30, 8, 0.4, $style, 'T');
                        $ye = 1;
                        $y2 += 1;
                        //$objekty->poznamka = 'lorem ipsum asdas ahs kasdh kh ask ksfdhf sdfh sdkfh askfh sdf lorem ipsum asdas ahs kasdh kh ask ksfdhf sdfh sdkfh askfh sdf lorem ipsum asdas ahs kasdh kh ask ksfdhf sdfh sdkfh askfh sdf ';
                        $poznamkaWidth -= $this->getX() - 15;
                    }
                }
                $x2 = $this->getX();
                $this->SetXY($x2, $y);

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'top') {
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    $this->setXY($x2, $this->GetY());
                    if ($isEan) {
                        $ye = 0;
                        $y2 -= 1;
                    }
                }

                $this->MultiCell(70, 5, $objekty->nazev, 0, 'L');

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'bottom') {
                    $this->setXY($x2, $this->GetY());
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    if ($isEan) {
                        $ye = 0;
                        $y2 -= 1;
                    }
                }

                $y2 = $this->GetY();


                $this->rollbackTransaction(true);

                $yH = $y2 - $y1 + $ye;
                $this->setCellPaddings('', '', '', '');

                if ($vybarvit) {
                    $this->Rect(15, $y1 - 0.1, 180, $yH - 0.1 + $ye, 'F');
                }

                if (!empty($objekty->ean)) {
                    if (is_numeric($objekty->ean) && (int)$objekty->ean > 0) {
                        $this->write1DBarcode($objekty->ean, 'c128', '', '', 30, 8, 0.4, $style, 'T');
                        $y2 += 1;
                    }
                }
                $x2 = $this->getX();
                $this->SetXY($x2, $y);

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'top') {
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    $this->setXY($x2, $this->GetY());
                    if ($isEan) {
                        $y2 -= 1;
                    }
                }

                $this->MultiCell($poznamkaWidth, 5, $objekty->nazev, 0, 'L', false, 1, '', '', true, 0, false, true, 0, 'B');

                if (!empty($objekty->poznamka) && $this->nastaveni->pozicePoznamky == 'bottom') {
                    $this->setXY($x2, $this->GetY());
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->MultiCell($poznamkaWidth, 3, $objekty->poznamka, 0, 'L', false);
                    $this->SetFont($this->nastaveni->font, '', 8);
                    if ($isEan) {
                        $y2 -= 1;
                    }
                }

                if (is_array($this->nastaveni->podtrzeni) && $this->pocetPolozek > $i) {
                    $this->SetDrawColor($this->nastaveni->podtrzeni[0], $this->nastaveni->podtrzeni[1], $this->nastaveni->podtrzeni[2]);
                    $this->SetLineWidth(0.2);
                    $this->Line(15, $y2 + $ye, 195, $y2 + $ye);
                    $this->SetDrawColor(0, 0, 0);
                }

                if ($objekty->special) {

                    if ($objekty->mnozstvi == 99) {
                        /* if ($this->nastaveni->sleva[1]==1) $this->slevaCelkem = (-1)*$objekty->cena/100 * $this->cenaCelkem;
                          else $this->slevaCelkem = $objekty->cena; */
                        $this->slevaCelkem = -$objekty->cena;

                        if ($this->nastaveni->typZaokrouhleni == 1) {
                            $this->cenaCelkem += $this->slevaCelkem;
                        } else {
                            $this->cenaCelkem += round($this->slevaCelkem, 2);
                        }

                        if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                            $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false), $y);
                            $sirkaSloupce = 0;
                            if ($this->nastaveni->zobrazeneSloupce['pocetmj']) {
                                $this->Cell(20, 5, 1, 0, 0, 'R');
                                $sirkaSloupce += 25;
                            }
                            if ($this->nastaveni->zobrazeneSloupce['mj']) {
                                $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce, $y);
                                $this->Cell(0, 5, '');
                                $sirkaSloupce += 15;
                            }
                            if ($this->nastaveni->zobrazeneSloupce['cenamj']) {
                                $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce, $y);
                                $this->Cell(25, 5, number_format($this->slevaCelkem, 2, ',', ' '), 0, 0, 'R');
                            }
                        }

                        $this->SetXY(170, $y);
                        $this->Cell(25, 5, number_format($this->slevaCelkem, 2, ',', ' '), 0, 0, 'R');
                    } else {
                        switch ($this->nastaveni->zpusobZaokrouhleni) {
                            case 2:
                                $zaokrouhleno = ceil($this->nastaveni->zaokrouhlit * ($this->cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            case 3:
                                $zaokrouhleno = floor($this->nastaveni->zaokrouhlit * ($this->cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            default:
                                $zaokrouhleno = round($this->nastaveni->zaokrouhlit * ($this->cenaCelkem), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit;
                                break;
                        }

                        $rozdil = $zaokrouhleno - $this->cenaCelkem;

                        if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                            $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false), $y);
                            $sirkaSloupce = 0;
                            if ($this->nastaveni->zobrazeneSloupce['pocetmj']) {
                                $this->Cell(20, 5, '', 0, 0, 'R');
                                $sirkaSloupce += 25;
                            }
                            if ($this->nastaveni->zobrazeneSloupce['mj']) {
                                $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce, $y);
                                $this->Cell(0, 5, '');
                                $sirkaSloupce += 15;
                            }
                            if ($this->nastaveni->zobrazeneSloupce['cenamj']) {
                                $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce, $y);
                                $this->Cell(25, 5, number_format($rozdil, 2, ',', ' '), 0, 0, 'R');
                            }
                        }

                        $this->SetXY(170, $y);
                        $this->Cell(25, 5, number_format($rozdil, 2, ',', ' '), 0, 0, 'R');
                        $this->cenaCelkem += $rozdil;
                    }
                } else {
                    // normalni polozka

                    if ($this->nastaveni->getPocetZobrazenychSloupcu() > 0) {
                        $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false), $y);
                        $sirkaSloupce = 0;
                        if ($this->nastaveni->zobrazeneSloupce['pocetmj']) {
                            $this->Cell(20, 5, $objekty->mnozstvi, 0, 0, 'R');
                            $sirkaSloupce += 25;
                        }
                        if ($this->nastaveni->zobrazeneSloupce['mj']) {
                            $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce, $y);
                            $this->Cell(0, 5, $objekty->mj);
                            $sirkaSloupce += 15;
                        }
                        if ($this->nastaveni->zobrazeneSloupce['cenamj']) {
                            $this->SetXY(95 + 65 - $this->nastaveni->getSirkaZobrazenychSloupcu(false) + $sirkaSloupce, $y);
                            $this->Cell(25, 5, number_format($objekty->cena, 2, ',', ' '), 0, 0, 'R');
                        }
                    }

                    $this->SetXY(170, $y);
                    $this->Cell(25, 5, number_format(($objekty->cena * $objekty->mnozstvi), 2, ',', ' '), 0, 0, 'R');

                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $this->cenaCelkem += ($objekty->cena * $objekty->mnozstvi);
                    } else {
                        $this->cenaCelkem += round($objekty->cena * $objekty->mnozstvi, 2);
                    }
                }
                $i++;
                $y = $y2 + $this->nastaveni->vzdalenost_polozek; //+ $plus;
            }
        }
        $this->SetTextColor(0, 0, 0);

        if (($this->cenaCelkem - $this->nastaveni->zalohy) > 0.5) {
            $konstanta = 213;
        } else {
            $konstanta = 202;
        }

        if ($this->nastaveni->platceDPH && $this->nastaveni->shrnutiDPH) {
            $konstanta -= 27;
        }

        $this->SetXY(15, $y + 5);
        $yLastItem = $this->getY();
        $this->SetFont($this->nastaveni->font, 'B', 9);
        $this->SetLineWidth(0.5);
        $this->Line(10.2, $y + 2, 199.8, $y + 2);
        $this->SetLineWidth(0.2);
        $posunutiX = 5; // udava o kolik posunout pravy vypis celkovych cen
        if ($this->nastaveni->platceDPH && $this->nastaveni->shrnutiDPH) {
            // shrnuti DPH
            $posunutiX = 0;
            $this->SetFont($this->nastaveni->font, 'B', 7.5);

            /*
              $this->SetFillColor(225);
              $this->Rect(10, $y + 2, 190, 10, 'F');
              $this->SetFont($this->nastaveni->font, 'B', 10);
              $this->SetXY(15, $y + 2);
              $this->Cell(0, 10, 'Dodavatel');
              $y += 10;
              $yLastItem += 10;
             */
            $this->SetXY(39, $y + 5);
            $this->Cell(27, 5, WFPf_preklad::t('zaklad'), 0, 0, 'R');
            $this->Cell(22, 5, WFPf_preklad::t('vyse_dph'), 0, 0, 'R');
            $this->Cell(26, 5, WFPf_preklad::t('celkem'), 0, 0, 'R');

            $this->SetLineWidth(0.1);
            $this->SetDrawColor(153, 153, 153);
            $this->Line(10.2, $y + 10, 115, $y + 10);

            $y += 11;
            $this->SetFont($this->nastaveni->font, '', 7.5);
            $celkove = array(0 => 0, 1 => 0, 2 => 0);

            foreach ($this->nastaveni->sazbyDPH as $k => $h) {
                /* if ($k == $this->nastaveni->GetSazba(2)) {
                  $konecnadan = $zakladDphCelkem[$k] * ($k / 100) + $rozdildph19;
                  //$zakladDphCelkem[$k]+= $vppH;
                  }
                  if ($k == $this->nastaveni->GetSazba(1)) {
                  $konecnadan = $zakladDphCelkem[$k] * ($k / 100) + $rozdildph9;
                  //$zakladDphCelkem[$k]+= $vppL;
                  } */
                $konecnadan = isset($dphCelkem[$k]) ? $dphCelkem[$k] : 0;
                if ($k == $this->nastaveni->GetSazba(0)) {
                    $konecnadan = 0;
                }

                if ($zakladDphCelkem[$k] != 0 || $this->nastaveni->shrnuti_prazdne) {
                    $this->SetXY(10, $y);
                    $this->Cell(26.5, 5, $h, 0, 0, 'L');
                    $this->Cell(10, 5, $k . ' %', 0, 0, 'R');
                    $this->Cell(19.5, 5, number_format($zakladDphCelkem[$k], 2, ',', ' '), 0, 0, 'R');
                    $this->Cell(22, 5, number_format($konecnadan, 2, ',', ' '), 0, 0, 'R');
                    $this->Cell(26, 5, number_format($zakladDphCelkem[$k] + $konecnadan, 2, ',', ' '), 0, 0, 'R');
                    $celkove[0] += $zakladDphCelkem[$k];
                    $celkove[1] += $konecnadan;
                    $celkove[2] += $zakladDphCelkem[$k] + $konecnadan;
                    $y += 5;
                }
            }

            $this->SetFont($this->nastaveni->font, 'B', 7.5);
            $this->Line(10.2, $y + 1, 115, $y + 1);
            $this->SetDrawColor(0, 0, 0);
            $y += 2;
            $this->SetXY(10, $y);
            $this->Cell(30, 5, WFPf_preklad::t('celkem_konec'), 0, 0, 'L');
            $this->Cell(26.5, 5, number_format($celkove[0], 2, ',', ' '), 0, 0, 'R');
            $this->Cell(22, 5, number_format($celkove[1], 2, ',', ' '), 0, 0, 'R');
            $this->Cell(26, 5, number_format($celkove[2], 2, ',', ' '), 0, 0, 'R');


            $this->SetLineWidth(0.3);
            $this->Line(10.2, $y + 6, 115, $y + 6);
            $this->SetLineWidth(0.2);

            $y += 5;

            if ($zaokrouhleno) {
                $this->SetXY(10, $y + 1);
                $this->SetFont($this->nastaveni->font, '', 7.5);
                $this->Cell(26, 5, WFPf_preklad::t('vcetne_zaokrouhleni'), 0, 0, 'L');
            }
        }
        $yPodpis = $this->getY();
        $this->SetFont($this->nastaveni->font, 'B', 10);
        $yLastItem += 0.25;
        $xCord = 118 - $posunutiX;
        $this->setXY($xCord, $yLastItem);

        if ($vslev) { // vypis slev
            if ($this->nastaveni->sleva[1] == 1) {
                $this->Cell(80, 5, WFPf_preklad::t('sleva'), 0);
                $this->SetXY(140 - $posunutiX, $yLastItem);
                $this->Cell(60, 5, number_format($this->nastaveni->sleva[0], 2, ',', ' ') . " %", 0, 0, 'R');
            } else {
                if ($this->nastaveni->zaokrouhlit > 0) {
                    $this->Cell(80, 5, WFPf_preklad::t('sleva_zaokrouhleno'), 0);
                } else {
                    $this->Cell(80, 5, WFPf_preklad::t('sleva'), 0);
                }

                $this->SetXY(140 - $posunutiX, $yLastItem);
                $this->Cell(60, 5, number_format(round($this->nastaveni->zaokrouhlit * ($this->nastaveni->sleva[0]), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit, 2, ',', ' ') . " " . $this->nastaveni->mena, 0, 0, 'R');

                // pokud se vypisuje sleva jen dole, tak prepocitej cenu
                if ($this->nastaveni->sleva[2] == 1) {
                    $this->cenaCelkem -= round($this->nastaveni->zaokrouhlit * ($this->nastaveni->sleva[0]), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit;
                }
            }
            $y += 7;
        } // konec slev
        else {
            $y -= 1;
            $yLastItem -= 7;
        }

        $this->SetXY($xCord, $yLastItem + 7);
        $this->SetFont($this->nastaveni->font, 'B', 10);
        $this->Cell($xCord, 5, WFPf_preklad::t('soucet_polozek') . ($this->nastaveni->vypisovatPocetPolozek ? " (" . $this->pocetPolozek . ")" : '') . ":", 0);
        $this->SetXY(140 - $posunutiX, $yLastItem + 7);
        $this->Cell(60, 5, number_format($this->cenaCelkem, 2, ',', ' ') . " " . $this->nastaveni->mena, 0, 0, 'R');

        // zalohy
        $this->SetXY($xCord, $yLastItem + 14);
        $this->Cell(41, 5, WFPf_preklad::t('uhrazene_zalohy'), 0, 0, 'L');
        $this->SetXY(140 - $posunutiX, $yLastItem + 14);
        $this->Cell(60, 5, number_format($this->nastaveni->zalohy, 2, ',', ' ') . " " . $this->nastaveni->mena, 0, 0, 'R');

        $this->SetXY($xCord, $yLastItem + 22);
        $this->SetFillColor($this->nastaveni->style['priceFillColor'][0], $this->nastaveni->style['priceFillColor'][1], $this->nastaveni->style['priceFillColor'][2]);
        $this->SetTextColor($this->nastaveni->style['priceFontColor'][0], $this->nastaveni->style['priceFontColor'][1], $this->nastaveni->style['priceFontColor'][2]);
        $this->Rect($xCord, $yLastItem + 20, 82, 9, 'F', $this->nastaveni->getBorders());
        $this->Cell(41, 5, WFPf_preklad::t('celkem_k_uhrade'), 0, 0, 'L');
        $this->SetXY(140 - $posunutiX, $yLastItem + 22);
        if ($this->nastaveni->zalohy > 0.5 && (($this->cenaCelkem - $this->nastaveni->zalohy) < 0.5) && (($this->cenaCelkem - $this->nastaveni->zalohy) > -0.5)) {
            $this->Cell(60, 5, number_format(0, 2, ',', ' ') . " " . $this->nastaveni->mena, 0, 0, 'R');
        } else {
            $this->Cell(60, 5, number_format(round($this->nastaveni->zaokrouhlit * ($this->cenaCelkem - $this->nastaveni->zalohy), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit, 2, ',', ' ') . " " . $this->nastaveni->mena, 0, 0, 'R');
        }
        if ($this->getY() > $yPodpis) {
            $yPodpis = $this->getY();
        }
        $this->paticka($yPodpis);
    }

    /**
     * Provede vypocet celkove ceny vcetne zapocitani vsech slev a zaokrouhleni a vrati vysledek jako pole
     * @return array Neplatce DPH :  array([cenaCelkem] => XXX) | U platce DPH  array([cenaCelkem] => XXX, [shrnuti_dph] => array([zaklad] => XXX, [dph] => ZZZ, [celkem] => YYY ))
     */
    public function vratKonecneCeny() {
        if ($this->nastaveni->platceDPH) {
            $zakladDphCelkem = array();
            $dphCelkem = array();
            foreach ($this->nastaveni->sazbyDPH as $k => $v) {
                $zakladDphCelkem[$k] = 0;
                $dphCelkem[$k] = 0;
            }
        }

        $i = 1;
        $polozky = $this->polozky;
        $pocetPolozek = $this->pocetPolozek;
        if (($this->nastaveni->sleva[0] > 0 || $this->nastaveni->sleva[3] == 1) && ($this->nastaveni->sleva[2] == 0 || $this->nastaveni->sleva[2] == 2) && $this->nastaveni->sleva[1] == 0) {
            $polozka = new WFPf_polozka('Sleva', 99, $this->nastaveni->sleva[0], 0, $this->nastaveni->sleva[4], true);
            if ($polozky[] = $polozka) {
                $pocetPolozek++;
            }
        }

        if ($this->nastaveni->zaokrouhlovat == 0) {
            // zaokrouhleni pro neplatce DPH
            $polozka = new WFPf_polozka(WFPf_preklad::t('zaokrouhleni'), 1, 0, 0, 0, true);
            if ($polozky[] = $polozka) {
                $pocetPolozek++;
            }
        }
        $cisloPolozky = 0;
        $zaokrouhleno = false;
        $slevaCelkem = 0;
        $cenaCelkem = 0;

        foreach ($polozky as $objekty) {
            $cisloPolozky++;

            // pokud je procentualni sleva, tak ji uplatnime na kazdou polozku
            if ($this->nastaveni->sleva[0] > 0 && $this->nastaveni->sleva[1] == 1) {
                $objekty->cena = $objekty->cena * ((100 - $this->nastaveni->sleva[0]) / 100);
            }

            if ($this->nastaveni->platceDPH) {
                // platci DPH
                if ($objekty->special) {
                    if ($objekty->mnozstvi == 99) {
                        $slevaCelkem = -$objekty->cena;

                        $cenaCelkem += $slevaCelkem;
                        $zakladDphCelkem[(string)$objekty->dph] += ($slevaCelkem);
                    } else {
                        switch ($this->nastaveni->zpusobZaokrouhleni) {
                            case 2:
                                $zaokrouhleno = ceil($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            case 3:
                                $zaokrouhleno = floor($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            default:
                                $zaokrouhleno = round($this->nastaveni->zaokrouhlit * ($cenaCelkem), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit;
                                break;
                        }

                        $rozdilSDPH = $zaokrouhleno - $cenaCelkem;
                        $cenaCelkem += $rozdilSDPH;
                        $pouziteSazby = array();
                        $maxSazba = 0;
                        $maxSazbaSum = 0;
                        foreach ($zakladDphCelkem as $kSazba => $castka) {
                            if ($castka != 0) {
                                $pouziteSazby[] = (float)$kSazba;
                            }

                            if ($castka > $maxSazbaSum) {
                                $maxSazba = (float)$kSazba;
                                $maxSazbaSum = $castka;
                            }
                        }
                        $sazbaZaokrouhleni = 0;

                        // zjistime do ktere sazby rozpocitame zaokrouhleni
                        switch ($this->nastaveni->zaokrouhleniRozpusteni) {
                            case 1:
                                // do nejvyssi pouzite sazby
                                $sazbaZaokrouhleni = max($pouziteSazby);
                                break;
                            case 2:
                                // do nejnizsi pouzite sazby
                                $sazbaZaokrouhleni = min($pouziteSazby);
                                break;
                            case 3:
                                // do sazby s nejvyssi celkovou castkou
                                $sazbaZaokrouhleni = $maxSazba;
                                break;
                        }
                        $zakladZaokrouhleni = (100 / (100 + $sazbaZaokrouhleni)) * $rozdilSDPH;
                        if ($this->nastaveni->typZaokrouhleni == 1) {
                            // pocitame s nezaokrouhlenymi
                            $zaokrouhleniDPH = $rozdilSDPH - $zakladZaokrouhleni;
                        } else {
                            // pociteme se zaokrouhlenymi
                            $zaokrouhleniDPH = $rozdilSDPH - round($zakladZaokrouhleni, 2);
                        }

                        if (!$this->nastaveni->reverseCharge) {
                            $dphCelkem[(string)$sazbaZaokrouhleni] += round($zaokrouhleniDPH, 2);
                            $zakladDphCelkem[(string)$sazbaZaokrouhleni] += round($zakladZaokrouhleni, 2);
                        } else {
                            $zaokrouhleniDPH = 0;
                            $zakladZaokrouhleni = $rozdilSDPH;
                            $zakladDphCelkem[(string)$sazbaZaokrouhleni] += round($rozdilSDPH, 2);
                        }
                        $zaokrouhleno = true;
                    }
                } else {
                    $cenaZaklad = $objekty->cena;
                    if ($this->nastaveni->castkySDPH) {
                        $cenaZaklad = $objekty->cena - ($objekty->cena * round(1 - (100 / (100 + $objekty->dph)), 4));
                    }

                    if (!$this->nastaveni->reverseCharge) {
                        $dphcko = $objekty->cena * $objekty->mnozstvi * $objekty->dph / 100;
                        if ($this->nastaveni->castkySDPH) {
                            $dphcko = $objekty->mnozstvi * ($objekty->cena * round(1 - (100 / (100 + $objekty->dph)), 4));
                        }
                    } else {
                        $dphcko = 0;
                    }

                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $dphCelkem[(string)$objekty->dph] += $dphcko;
                        $zakladDphCelkem[(string)$objekty->dph] += ($cenaZaklad * $objekty->mnozstvi);
                    } else {
                        $dphCelkem[(string)$objekty->dph] += round($dphcko, 2);
                        $zakladDphCelkem[(string)$objekty->dph] += round($cenaZaklad * $objekty->mnozstvi, 2);
                    }

                    $cenaCelkem = $cenaZaklad * $objekty->mnozstvi + $dphcko;
                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $cenaCelkem += $cenaCelkem;
                    } else {
                        $cenaCelkem += round($cenaCelkem, 2);
                    }
                }
                $i++;
            } else {
                // neplatci DPH
                if ($objekty->special) {

                    if ($objekty->mnozstvi == 99) {
                        $slevaCelkem = -$objekty->cena;

                        if ($this->nastaveni->typZaokrouhleni == 1) {
                            $cenaCelkem += $slevaCelkem;
                        } else {
                            $cenaCelkem += round($slevaCelkem, 2);
                        }
                    } else {
                        switch ($this->nastaveni->zpusobZaokrouhleni) {
                            case 2:
                                $zaokrouhleno = ceil($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            case 3:
                                $zaokrouhleno = floor($this->nastaveni->zaokrouhlit * ($cenaCelkem)) / $this->nastaveni->zaokrouhlit;
                                break;
                            default:
                                $zaokrouhleno = round($this->nastaveni->zaokrouhlit * ($cenaCelkem), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit;
                                break;
                        }

                        $rozdil = $zaokrouhleno - $cenaCelkem;
                        $cenaCelkem += $rozdil;
                    }
                } else {
                    // normalni polozka
                    if ($this->nastaveni->typZaokrouhleni == 1) {
                        $cenaCelkem += ($objekty->cena * $objekty->mnozstvi);
                    } else {
                        $cenaCelkem += round($objekty->cena * $objekty->mnozstvi, 2);
                    }
                }
                $i++;
            }
        }

        if ($this->nastaveni->platceDPH) {
            $return = array();
            if (!isset($celkove)) {
                $celkove = array(0 => 0, 1 => 0, 2 => 0);
            }
            foreach ($this->nastaveni->sazbyDPH as $k => $h) {
                $konecnadan = isset($dphCelkem[$k]) ? $dphCelkem[$k] : 0;
                if ($k == $this->nastaveni->GetSazba(0)) {
                    $konecnadan = 0;
                }

                if ($zakladDphCelkem[$k] != 0) {
                    $return[$k] = array('zaklad' => round($zakladDphCelkem[$k], 2), 'dph' => round($konecnadan, 2), 'celkem' => round($zakladDphCelkem[$k] + $konecnadan, 2));
                    $celkove[0] += $zakladDphCelkem[$k];
                    $celkove[1] += $konecnadan;
                    $celkove[2] += $zakladDphCelkem[$k] + $konecnadan;
                }
            }
            return array('cena_celkem' => $cenaCelkem, 'shrnuti_dph' => $return);
        } else {
            return array('cena_celkem' => $cenaCelkem);
        }
    }

    private function paticka($y = 0) {
        $this->SetTextColor(0, 0, 0);
        if ($y == 0) {
            $y = $this->GetY();
        }

        if ($this->nastaveni->zalohy > 0 && ($this->cenaCelkem - $this->nastaveni->zalohy) < 0.5) {
            $this->SetXY(120, $this->GetY() + 8);
            $this->SetFont($this->nastaveni->font, 'B', 11);
            $this->Cell(80, 5, WFPf_preklad::t('jiz_uhrazeno_neplatit'), 0, 0, 'R');
        }

        if ($this->nastaveni->reverseCharge) {
            $this->SetFont($this->nastaveni->font, 'B', 7);
            $this->SetXY(15, $y + 5);
            $this->MultiCell(100, 0, ($this->nastaveni->reverseChargeText == '' ? WFPf_preklad::t('reverse_charge_cz') : $this->nastaveni->reverseChargeText), 0, 'L', false);
            $y = $this->GetY() - 10;
        }

        if (($this->nastaveni->typDokladu == 3 || $this->nastaveni->typDokladu == 4) && count($this->nastaveni->doplnujiciInformace) > 0) {
            // ODD/dobropis nebo storno - vypiseme pod shrnuti DPH jeste duvod opravy
            $this->SetFont($this->nastaveni->font, 'B', 7);
            $this->SetXY(15, $y + 5);
            $this->Cell(0, 10, WFPf_preklad::t('doplnujici_informace'));
            $this->SetFont($this->nastaveni->font, '', 7);
            $y += 10;
            $this->SetXY(15, $y);
            if (isset($this->nastaveni->doplnujiciInformace['puvodniDoklad']) && !empty($this->nastaveni->doplnujiciInformace['puvodniDoklad'])) {
                switch ($this->nastaveni->typDokladu) {
                    case 3:
                        // ODD/dobropis
                        if ($this->nastaveni->platceDPH) {
                            $this->Cell(95, 10, WFPf_preklad::t('odd_k_fakture') . $this->nastaveni->doplnujiciInformace['puvodniDoklad']);
                        } else {
                            $this->Cell(95, 10, WFPf_preklad::t('dobropis_k_fakture') . $this->nastaveni->doplnujiciInformace['puvodniDoklad']);
                        }
                        break;
                    case 4:
                        // storno
                        $this->Cell(95, 10, WFPf_preklad::t('storno_k_fakture') . $this->nastaveni->doplnujiciInformace['puvodniDoklad']);
                        break;
                }
                $y += 7;
            }

            if (isset($this->nastaveni->doplnujiciInformace['duvod']) && !empty($this->nastaveni->doplnujiciInformace['duvod'])) {
                $this->SetXY(15, $y);
                if ($this->nastaveni->typDokladu == 3) {
                    $this->MultiCell(100, 0, WFPf_preklad::t('duvod_opravy') . $this->nastaveni->doplnujiciInformace['duvod'], 0, 'L', false);
                } else {
                    $this->MultiCell(100, 0, $this->nastaveni->doplnujiciInformace['duvod'], 0, 'L', false);
                }
                $y = $this->GetY() + 2;
            } else {
                $y += 2;
            }

            $this->SetLineWidth(0.3);

            $this->Line(10.2, $y, 115, $y);
            $this->SetLineWidth(0.2);
            $y -= 8;
        }

        $this->vPodpis = $y + 35;
        $this->DashedLine(120, $y + 35, 190, 30);
        $this->SetXY(120, $y + 35);
        $this->SetFont($this->nastaveni->font, '', 7);
        $this->Cell(70, 5, WFPf_preklad::t('razitko_a_podpis'), 0, 0, 'C');

        if (isset($this->nastaveni->text_podpis[0]) && $this->nastaveni->text_podpis[0] != '') {
            $this->SetXY(15, $y + 10);
            $this->SetFont($this->nastaveni->font, '' . $this->nastaveni->text_podpis[2] . '', $this->nastaveni->text_podpis[1]);
            $this->MultiCell(95, $this->nastaveni->text_podpis[1] - 4, $this->nastaveni->text_podpis[0], 0, 'L', false);
        }

        if (isset($this->nastaveni->eet['eet'])) {
            $this->SetXY(15, $this->GetY() + 2);
            $this->SetFont($this->nastaveni->font, 'B', 10);
            $this->Cell(180, 5, WFPf_preklad::t('eet'), 0, 0, 'L');
            $this->SetY($this->GetY() + 5);
            $this->SetFont($this->nastaveni->font, '', 7);
            if (isset($this->nastaveni->eet['provozovna'])) {
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t('provozovna') . '</strong>: ' . $this->nastaveni->eet['provozovna'], 0, 1);
            }
            if (isset($this->nastaveni->eet['pokladna'])) {
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t('pokladna') . '</strong>: ' . $this->nastaveni->eet['pokladna'], 0, 1);
            }
            if (isset($this->nastaveni->eet['datum'])) {
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t('datum_a_cas') . '</strong>: ' . date('d.m.Y H:i:s', $this->nastaveni->eet['datum']), 0, 1);
            }
            if (isset($this->nastaveni->eet['rezim'])) {
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t('rezim') . '</strong>: ' . $this->nastaveni->eet['rezim'], 0, 1);
            }
            if (isset($this->nastaveni->eet['fik'])) {
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t('fik') . '</strong>: ' . $this->nastaveni->eet['fik'], 0, 1);
            }
            if (isset($this->nastaveni->eet['bkp'])) {
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t('bkp') . '</strong>: ' . $this->nastaveni->eet['bkp'], 0, 1);
            }
            if (isset($this->nastaveni->eet['pkp'])) {
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t('pkp') . '</strong>: ' . $this->nastaveni->eet['pkp'], 0, 1);
            }
            $preddefinovaneKlice = array('eet', 'provozovna', 'pokladna', 'pkp', 'bkp', 'fik', 'rezim', 'datum');
            foreach ($this->nastaveni->eet as $k => $v) {
                if (in_array($k, $preddefinovaneKlice)) {
                    continue;
                }
                $this->writeHTMLCell(180, 4, 15, $this->GetY(), '<strong>' . WFPf_preklad::t($k) . '</strong>: ' . $v, 0, 1);
            }
        }

        // QR Platba, pokud ma byt jen v paticce
        if ($this->nastaveni->qr_platba['vypisovat'] && $this->nastaveni->qr_platba['strana'] == 'PA') {
            $this->RenderQRPlatba($this->nastaveni->qr_platba['x'], $this->GetY() + $this->nastaveni->qr_platba['y']);
        }
    }

    private function RenderQRPlatba($x, $y) {
        if (!$this->nastaveni->qr_platba['vypisovat']) {
            return false;
        }

        // vykreslit QR kod pro platbu
        $kodBanky = $this->platebniUdaje->kodbanky[1];
        $cisloUctu = $this->platebniUdaje->cislouctu[1];

        if (strpos($cisloUctu ?? '', '/')) {
            $exploded = explode('/', $cisloUctu);
            $cisloUctu = trim($exploded[0]);
            $kodBanky = trim($exploded[1]);
        }
        $iban = WFPf_iban::getIban($cisloUctu, $kodBanky);
        if ($iban) {
            $QRParameters = array('iban' => $iban, 'amount' => (round($this->nastaveni->zaokrouhlit * ($this->cenaCelkem - $this->nastaveni->zalohy), $this->nastaveni->zaokrouhlovat) / $this->nastaveni->zaokrouhlit), 'vs' => $this->platebniUdaje->variabilnisymbol[1], 'ks' => $this->platebniUdaje->konstantnisymbol[1], 'ss' => $this->platebniUdaje->specifickysymbol[1]);
            if ($QRParameters['amount'] <= 0) {
                return false;
            }
            if (WFPf_iban::getSwift($kodBanky)) {
                $QRParameters['bic'] = WFPf_iban::getSwift($kodBanky);
            }
            $style = array('position' => '', 'border' => 0, 'hpadding' => 0, 'vpadding' => 0, 'fgcolor' => array(0, 0, 0), 'bgcolor' => false);
            $this->write2DBarcode(WFPf_iban::getQRString($QRParameters), 'QRCODE,M', $x, $y, $this->nastaveni->qr_platba['velikost'], $this->nastaveni->qr_platba['velikost'], $style);

            switch ($this->nastaveni->qr_platba['styl']) {
                case 2:
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->setXY($x + $this->nastaveni->qr_platba['velikost'] - 0.5, $y + 1 + $this->nastaveni->qr_platba['velikost'] / 2 + $this->GetStringWidth(WFPf_preklad::t('qr_platba')) / 2);
                    $this->StartTransform();
                    $this->Rotate(90);
                    $this->Cell(15, 0, WFPf_preklad::t('qr_platba'));
                    $this->StopTransform();
                    break;
                case 3:
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->Text($x - 0.5, $y + $this->nastaveni->qr_platba['velikost'] - 0.5, WFPf_preklad::t('qr_platba'));
                    break;
                default:
                    $this->SetFont($this->nastaveni->font, '', 6);
                    $this->Rect($x - 1, $y - 1, $this->nastaveni->qr_platba['velikost'] + 2, $this->nastaveni->qr_platba['velikost'] + 2.5);
                    $this->Rect($x, $y + $this->nastaveni->qr_platba['velikost'] + 0.5, $this->GetStringWidth(WFPf_preklad::t('qr_platba')) + 1, 2, 'F', array(), array(255, 255, 255));
                    $this->Text($x - 0.5, $y + $this->nastaveni->qr_platba['velikost'] - 0.5, WFPf_preklad::t('qr_platba'));
                    break;
            }
        } else {
            return false;
        }
    }

    private function vypisAdresu(WFPf_adresa $trida, float $x, float $y, float $velikost, bool $posunY = false) {
        $vynechan = false;
        $vypis = '';
        $arrKeys = array_keys($trida->getProperties());
        $posledniKlic = end($arrKeys);

        foreach ($trida->getProperties() as $klice => $hodnoty) {
            if ($klice == "psc") {
                $znak = " ";
            } else {
                $znak = "\n";
            }

            if (trim($hodnoty) != '') {
                switch ($klice) {
                    case 'ic':
                        $hodnoty = WFPf_preklad::t('ic') . "       " . $hodnoty;
                        break;
                    case 'dic':
                        $hodnoty = WFPf_preklad::t('dic') . "     " . $hodnoty;
                        break;
                    case 'email':
                        $hodnoty = WFPf_preklad::t('email') . " " . $hodnoty;
                        break;
                    case 'telefon':
                        $hodnoty = WFPf_preklad::t('tel') . "    " . $hodnoty;
                        break;
                    case 'web':
                        $hodnoty = WFPf_preklad::t('web') . "    " . $hodnoty;
                        break;
                }

                if ($hodnoty == "--") {
                    if (!$vynechan) {
                        $vypis .= $znak;
                        $vynechan = true;
                    } else {
                        $vynechan = false;
                    }
                } else {
                    $vypis .= $hodnoty . $znak;
                    $vynechan = false;
                }
            }

            if ($klice == $posledniKlic) {
                break;
            }
        }
        $this->SetFont($this->nastaveni->font, '', $velikost);
        $this->SetXY($x, $y);
        $this->MultiCell(0, 4, $vypis);

        if ($posunY) {
            if ($this->GetY() > $this->konstantaY) {
                $this->konstantaY = $this->GetY();
            }
            /*
              if ($this->GetY() < 69) {
              $this->konstantaY = 10;
              } else {
              $this->konstantaY = $this->GetY() - 61;
              } */
        }
    }

    /**
     * @param WFPf_informace|WFPf_platebniUdaje $trida
     * @param float $x
     * @param float $y
     * @param float $velikost
     * @param float $mezera
     * @return float|int
     */
    private function vypisArrayCell(WFPf_informace|WFPf_platebniUdaje $trida, float $x, float $y, float $velikost, float $mezera) {
        $i = 1;
        $this->SetXY($x, $y);
        foreach ($trida->getProperties() as $klice => $hodnoty) {
            if ($klice == "splatnost" || $klice == "variabilnisymbol") {
                $this->SetFont($this->nastaveni->font, 'B', $velikost);
            } else {
                $this->SetFont($this->nastaveni->font, '', $velikost);
            }

            $this->SetX($x);
            if (isset($hodnoty[1])) {
                if (isset($hodnoty[2]) && $hodnoty[2]) {
                    if ($this->nastaveni->platceDPH) {
                        $dale = true;
                    } else {
                        $dale = false;
                    }
                } else {
                    $dale = true;
                }

                if ($dale) {
                    foreach ($hodnoty as $k => $hodnota) {
                        if ($k == 2) {
                            break;
                        }
                        $this->Cell(0, 5, $hodnota, 0, 1);
                        $this->SetXY($x + $mezera, ($y + ($i - 1) * 5));
                    }
                    $this->SetXY($x + $mezera, ($y + $i * 5));
                    $i++;
                }
            }
        }
        return ($i * 5);
    }

}

class WFPf_adresa {

    public string $firma;
    public string $jmeno;
    public string $ulice;
    public string $psc;
    public string $mesto;
    public string $zeme = 'Česká republika';
    public string $mezera = '--';
    public string $ic;
    public string $dic;
    public string $mezera2 = '--';
    public string $telefon;
    public string $email;
    public string $web;


    /**
     * Přeloží současnou zemi
     * @return self
     */
    public function preklad(): self {
        $this->zeme = WFPf_preklad::t($this->zeme);
        return $this;
    }

    /**
     * Get non empty properties of this class
     * @return array
     */
    public function getProperties(): array {
        $return = array();
        foreach ($this as $key => $val) {
            if (empty($val)) {
                continue;
            }
            $return[$key] = $val;
        }
        return $return;
    }

    /**
     * Nastavení názvu firmy nebo jména
     * @param string $firma
     * @return self
     */
    public function SetFirma(string $firma): self {
        $this->firma = $firma;
        return $this;
    }

    /**
     * Nastavení státu
     * @param string $zeme
     * @return self
     */
    public function SetZeme(string $zeme): self {
        $this->zeme = $zeme;
        return $this;
    }

    /**
     * Nastavení telefoního čísla
     * @param string $telefon
     * @return self
     */
    public function SetTelefon(string $telefon): self {
        $this->telefon = $telefon;
        return $this;
    }

    /**
     * Nastvení emailové adresy
     * @param string $email
     * @return self
     */
    public function SetEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    /**
     * Nastavení webové adresy
     * @param string $web
     * @return self
     */
    public function SetWeb(string $web): self {
        $this->web = $web;
        return $this;
    }

    /**
     * Nastavení jména
     * @param string $jmeno
     * @return self
     */
    public function SetJmeno(string $jmeno): self {
        $this->jmeno = $jmeno;
        return $this;
    }

    /**
     * Nastavení ulice
     * @param string $ulice
     * @return self
     */
    public function SetUlice(string $ulice): self {
        $this->ulice = $ulice;
        return $this;
    }

    /**
     * Nastavení PSČ
     * @param string $psc
     * @return self
     */
    public function SetPSC(string $psc): self {
        $this->psc = $psc;
        return $this;
    }

    /**
     * Nastavení města
     * @param string $mesto
     * @return self
     */
    public function SetMesto(string $mesto): self {
        $this->mesto = $mesto;
        return $this;
    }

    /**
     * Nastavení IČ
     * @param string $ic
     * @return self
     */
    public function SetIC(string $ic): self {
        $this->ic = $ic;
        return $this;
    }

    /**
     * Nastavení DIČ
     * @param string $dic
     * @return self
     */
    public function SetDIC(string $dic): self {
        $this->dic = $dic;
        return $this;
    }

}

class WFPf_informace {

    public array $objednavka = array('Na základě objednávky č.:', NULL, false);
    public array $zedne = array('Ze dne:', NULL, false);
    public array $vystaveni = array('Datum vystavení:', NULL, false);
    public array $splatnost = array('Datum splatnosti:', NULL, false);
    public array $plneni = array('Datum zdanitelného plnění:', NULL, true);

    public array $dynamicProperties = [];
    private int $pocetParametru = 0;

    /**
     * Provede překlad základních údajů do aktuálního jazyka
     * @return self
     */
    public function preklad(): self {
        $this->objednavka[0] = WFPf_preklad::t('na_zaklade_objednavky_c');
        $this->zedne[0] = WFPf_preklad::t('ze_dne');
        $this->vystaveni[0] = WFPf_preklad::t('datum_vystaveni');
        $this->splatnost[0] = WFPf_preklad::t('datum_splatnosti');
        $this->plneni[0] = WFPf_preklad::t('datum_zdanitelneho_plneni');
        return $this;
    }

    /**
     * Get static and dynamic properties of this class
     * @return array
     */
    public function getProperties(): array {
        $return = array();
        foreach ($this as $key => $val) {
            $return[$key] = $val;
        }
        if (!empty($this->dynamicProperties)) {
            foreach ($this->dynamicProperties as $key => $val) {
                $return[$key] = $val;
            }
        }
        return $return;
    }

    /**
     * Přidání vlastního parametru do sekce informace
     * @param string $nazev
     * @param string $hodnota
     * @return self
     */
    public function AddParametr(string $nazev, string $hodnota): self {
        if (!empty($nazev) && !empty($hodnota)) {
            $this->pocetParametru++;
            $klic = 'parametr' . $this->pocetParametru;
            if (!isset($this->dynamicProperties[$klic])) {
                $this->dynamicProperties[$klic] = array($nazev, $hodnota);
            }
        }
        return $this;
    }

    /**
     * Nastavení čísla objednávky, na základě kterého byl doklad vystaven
     * @param string $objednavka
     * @return self
     */
    public function SetObjednavka(string $objednavka): self {
        if (!empty($objednavka)) {
            $this->objednavka[1] = $objednavka;
        }
        return $this;
    }

    /**
     * Nastavení data přijetí objednávky
     * @param string $datum
     * @return self
     */
    public function SetZedne(string $datum): self {
        if (!empty($datum)) {
            $this->zedne[1] = $datum;
        }
        return $this;
    }

    /**
     * Nastavení datumu vystavení dokladu
     * @param string $datum
     * @return self
     */
    public function SetVystaveni(string $datum): self {
        if (!empty($datum)) {
            $this->vystaveni[1] = $datum;
        }
        return $this;
    }

    /**
     * Nastavení datumu splatnosti
     * @param string $datum
     * @return self
     */
    public function SetSplatnost(string $datum): self {
        if (!empty($datum)) {
            $this->splatnost[1] = $datum;
        }
        return $this;
    }

    /**
     * Nastavení datumu zdanitelného plnění
     * @param string $datum
     * @return self
     */
    public function SetPlneni(string $datum): self {
        if (!empty($datum)) {
            $this->plneni[1] = $datum;
        }
        return $this;
    }

}

class WFPf_platebniUdaje {

    public array $zpusobuhrady = array('Způsob úhrady:', NULL, false);
    public array $cislouctu = array('Číslo účtu:', NULL, false);
    public array $kodbanky = array('Kód banky:', NULL, false);
    public array $variabilnisymbol = array('Variabilní symbol:', NULL, false);
    public array $konstantnisymbol = array('Konstantní symbol:', NULL, false);
    public array $specifickysymbol = array('Specifický symbol:', NULL, false);

    public array $dynamicProperties = [];
    private $pocetParametru = 0;

    /**
     * Provede překlad textů z této sekce do aktuálně nastaveného jazyka
     * @return self
     */
    public function preklad(): self {
        $this->zpusobuhrady[0] = WFPf_preklad::t('zpusob_uhrady');
        $this->cislouctu[0] = WFPf_preklad::t('cislo_uctu');
        $this->kodbanky[0] = WFPf_preklad::t('kod_banky');
        $this->variabilnisymbol[0] = WFPf_preklad::t('variabilni_symbol');
        $this->konstantnisymbol[0] = WFPf_preklad::t('konstantni_symbol');
        $this->specifickysymbol[0] = WFPf_preklad::t('specificky_symbol');
        return $this;
    }

    /**
     * Get static and dynamic properties of this class
     * @return array
     */
    public function getProperties(): array {
        $return = array();
        foreach ($this as $key => $val) {
            $return[$key] = $val;
        }
        if (!empty($this->dynamicProperties)) {
            foreach ($this->dynamicProperties as $key => $val) {
                $return[$key] = $val;
            }
        }
        return $return;
    }

    /**
     * Přidání vlastního parametru do sekce platební údaje
     * @param string $nazev
     * @param string $hodnota
     * @return self
     */
    public function AddParametr(string $nazev, string $hodnota): self {
        if (!empty($nazev) && !empty($hodnota)) {
            $this->pocetParametru++;
            $klic = 'parametr' . $this->pocetParametru;
            if (!isset($this->dynamicProperties[$klic])) {
                $this->dynamicProperties[$klic] = array($nazev, $hodnota);
            }
        }
        return $this;
    }

    /**
     * Nastavení způsobu úhrady (převodem, hotově....)
     * @param string $zpusobUhrady
     * @return self
     */
    public function SetZpusobuhrady(string $zpusobUhrady): self {
        if (!empty($zpusobUhrady)) {
            $this->zpusobuhrady[1] = $zpusobUhrady;
        }
        return $this;
    }

    /**
     * Nastavení konstantního symbolu
     * @param string $ks
     * @return self
     */
    public function SetKS(string $ks): self {
        if (!empty($ks)) {
            $this->konstantnisymbol[1] = $ks;
        }
        return $this;
    }

    /**
     * Nastavení specifického symbolu
     * @param string $ss
     * @return self
     */
    public function SetSS(string $ss): self {
        if (!empty($ss)) {
            $this->specifickysymbol[1] = $ss;
        }
        return $this;
    }

    /**
     * Nastavení čísla účtu - bez kódu banky
     * @param string $cisloUctu
     * @return self
     */
    public function SetCislouctu(string $cisloUctu): self {
        if (!empty($cisloUctu)) {
            $this->cislouctu[1] = $cisloUctu;
        }
        return $this;
    }

    /**
     * Nastavení kódu banky
     * @param string $kodBanky
     * @return self
     */
    public function SetKodbanky(string $kodBanky): self {
        if (!empty($kodBanky)) {
            $this->kodbanky[1] = $kodBanky;
        }
        return $this;
    }

    /**
     * Nastavení variabilního symbolu
     * @param string $vs
     * @return self
     */
    public function SetVS(string $vs): self {
        if (!empty($vs)) {
            $this->variabilnisymbol[1] = $vs;
        }
        return $this;
    }

}

class WFPf_polozka {

    public string $nazev;
    public float $mnozstvi;
    public string $mj;
    public float $cena;
    public float $dph;
    public bool $special = false;
    public ?int $ean = null;
    public string $poznamka = '';

    /**
     * Přidání nové položky dokladu
     * @param string $nazev
     * @param float $mnozstvi
     * @param float $cena
     * @param string $mj
     * @param float $dph
     * @param bool $special
     * @param int|null $ean
     * @param string $poznamka
     * @return self
     */
    public function __construct(string $nazev, float $mnozstvi, float $cena, string $mj = '', float $dph = 0, bool $special = false, ?int $ean = null, string $poznamka = '') {
        $this->nazev = $nazev;
        $this->mnozstvi = $mnozstvi;
        $this->cena = $cena;
        $this->mj = $mj;
        $this->dph = $dph;
        $this->special = (bool)$special;
        $this->ean = $ean;
        $this->poznamka = $poznamka;
        return $this;
    }

}

class WFPf_email {

    public string $address; // adresa pro zaslani faktury na email
    public string $body; // text emailu
    public string $subject; // predmet emailu
    public string $from;  // adresa odesilatele
    public string $fromName;  // jmeno odesilatele
    public string $phpMailerPath; // cesta k PHPmaileru

    /**
     * Nastavení emailové adresy, na kterou chceme doklad odeslat
     * @param string $email
     * @return self
     */
    public function SetAddress(string $email): self {
        $this->address = $email;
        return $this;
    }

    /**
     * Nastavení těla emailu
     * @param string $body
     * @return self
     */
    public function SetBody(string $body): self {
        $this->body = $body;
        return $this;
    }

    /**
     * Nastavení předmětu emailu
     * @param string $subject
     * @return self
     */
    public function SetSubject(string $subject): self {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Nastavení emailu odesílatele
     * @param string $emailFrom
     * @return self
     */
    public function SetFrom(string $emailFrom): self {
        $this->from = $emailFrom;
        return $this;
    }

    /**
     * @deprecated
     * @deprecated 2.3.001
     * @deprecated Use method SetFromName
     */
    public function SetFrom_name(string $value): self {
        $this->SetFromName($value);
        return $this;
    }

    /**
     * Nastavení jména odesilatele
     * @param string $fromName
     * @return self
     */
    public function SetFromName(string $fromName): self {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * @deprecated
     * @deprecated 2.3.001
     * @deprecated Use method SetPhpMailerPath
     */
    public function SetPhp_mailer_path($value) {
        $this->SetPhpMailerPath($value);
    }

    /**
     * Set path to PHPMailer class file for inclusion.
     * @param string $phpMailerPath
     * @return self
     */
    public function SetPhpMailerPath(string $phpMailerPath): self {
        $this->phpMailerPath = $phpMailerPath;
        return $this;
    }

}

class WFPf_nastaveni {

    public ?string $cislo_faktury;
    public string $autor;
    public string $titulek;
    public string $mena = "CZK";
    public string $jmenosouboru = ''; // ulozit do souboru
    public bool $platceDPH = false; // uvadi zda je ci neni platce DPH
    public float $vzdalenost_polozek = 0; // vzdalenost fakturovanych polozek
    public bool|array $podtrzeni = false; // podtrhavat fakturovane polozky
    public float $zalohy = 0; // uhrazene zalohy
    public int $zaokrouhlit = 1; // 2 = zaokrouhleni na padesatniky, 1 = zaokrouhleni na koruny
    public array $text_podpis = array(); // text vlevo od podpisu (text,[velikost=8],[styl UBI])
    public array $text_konec = array(); // text na konci faktury (text,[velikost=8],[styl UBI])
    public array $obrazek = array(); // pole obrazku - obrazek (cesta,horizontalni_pozice,vertikalni_pozice,[opakovat=false],[sirka],[vyska])
    public array $sazbyDPH = array('0' => 'Nulová sazba', '12' => 'Snížená sazba', '21' => 'Základní sazba');
    public int $zaokrouhlovat = 0;
    public int $zaokrouhleniRozpusteni = 1; // Rozpousteni do nejvyssi sazby DPH na dokladu
    public bool $zaokrouhleniPolozka = true; // Zda pocitat zaokrouhleni jako polozku faktury
    public bool $shrnutiDPH = true;
    public bool $jizUhrazenoVPlatebnichUdajich = false;
    public array $zobrazeneSloupce = array('mj' => true, 'pocetmj' => true, 'cenamj' => true);
    public array $sleva = array(0, 0, 1, 0); // sleva - prvni cislo udava hodnotu slevy, druhe udava zda je to sleva v korunach (0) nebo procentualni sleva (1), treti udava kde vypsat slevu 0 = mezi polozkami, 1=dole u celkove ceny, 2=polozky i dole, ctvrty parametr udava zda ma byt vypsana sleva i kdyz je nulova
    public bool $send_to_mail = false; // urcuje zda se ma vygenerovana faktura zaslat na mail
    public bool $shrnuti_prazdne = true; // urcuje zda se maji vypsat i nulove polozky ve shrnuti DPH
    public string $podpis = ''; // Obsah souboru s certifikatem
    public string $podpis_heslo = '';
    public int $typZaokrouhleni = 1;
    public int $zpusobZaokrouhleni = 1;
    public int $typDokladu = 1;
    public array $podpis_info = array(
        'Name' => '',
        'Location' => '',
        'Reason' => '',
        'ContactInfo' => '',
    );
    public array $style = array('fillColor' => array(225, 225, 225), 'fontColor' => array(0, 0, 0), 'priceFillColor' => array(225, 225, 225), 'priceFontColor' => array(0, 0, 0), 'itemFillColor' => array(192, 192, 192), 'itemFontColor' => array(0, 0, 0)); // styl designu
    public array $konecnyPrijemce = array('vypisovat' => false, 'jina_adresa' => false);
    public array $qr_platba = array('vypisovat' => false, 'x' => 50, 'y' => 50, 'strana' => 'F', 'velikost' => 20, 'styl' => 1);
    public string $outputType = 'I'; // defaultni output typ = interaktivni zobrazeni v prohlizeci
    public array $carovyKod = array('kod' => 0, 'top' => 1, 'left' => 10, 'width' => 30, 'height' => 10);
    public array $doplnujiciInformace = array();
    public bool $reverseCharge = false;
    public string $reverseChargeText = '';
    public string $font = 'dejavusans';
    public bool $vypisovatPocetPolozek = true;
    public bool $castkySDPH = false;
    public array $eet = array();
    public string $pozicePoznamky = 'bottom';
    public array $borders = array('enabled' => false);
    public string $nazevDokladu = '';

    /**
     * Change hexdec code to array with RGB decimal values
     * @param $htmlColor
     * @return array
     */
    private function getRGBColor($htmlColor): array {
        return array(hexdec(substr($htmlColor, 0, 2)), hexdec(substr($htmlColor, 2, 2)), hexdec(substr($htmlColor, 4, 2)));
    }

    /**
     * Vrací počet zobrazených sloupců MJ
     * @return int
     */
    public function getPocetZobrazenychSloupcu(): int {
        $pocet = 0;
        foreach ($this->zobrazeneSloupce as $sloupec) {
            if ($sloupec) {
                $pocet++;
            }
        }
        return $pocet;
    }

    /**
     * Vypočítá a vrátí celkovou šířku oblasti s vypsanými sloupci MJ
     * @param bool $platceDPH
     * @return int
     */
    public function getSirkaZobrazenychSloupcu(bool $platceDPH = true): int {
        $sirka = 0;
        if ($platceDPH) {
            $sirky = array('mj' => 8, 'pocetmj' => 17, 'cenamj' => 10);
        } else {
            $sirky = array('mj' => 25, 'pocetmj' => 15, 'cenamj' => 25);
        }
        foreach ($this->zobrazeneSloupce as $nazev => $sloupec) {
            if ($sloupec) {
                $sirka += $sirky[$nazev];
            }
        }
        return $sirka;
    }

    /**
     * Nastavi individualni nazev dokladu
     * @param string $nazev
     * @return self
     */
    public function setNazevDokladu($nazev): self {
        $this->nazevDokladu = $nazev;
        return $this;
    }

    /**
     * Nastavi zobrazení nápisu "již urahezno - neplatit" přímo v sekci platebních údajů
     * @param bool $bool
     * @return self
     */
    public function setJizUhrazenoVPlatebnichUdajich(bool $bool): self {
        $this->jizUhrazenoVPlatebnichUdajich = $bool;
        return $this;
    }

    /**
     * Reset class settings for invoice data
     * @return self
     */
    public function clear(): self {
        $this->cislo_faktury = null;
        $this->typDokladu = 1;
        $this->sleva = array(0, 0, 1, 0);
        $this->mena = "CZK";
        $this->zalohy = 0;
        $this->eet = array();
        return $this;
    }

    /**
     * Vypisovat ve shrnutí součet celkového počtu položek?
     * @param boolean $val
     * @return self
     */
    public function setVypisovatPocetPolozek(bool $val): self {
        $this->vypisovatPocetPolozek = (bool)$val;
        return $this;
    }

    /**
     * Určuje pozici poznámky u položek
     * @param string $val top|bottom
     */
    public function setPozicePoznamky(string $pozice): self {
        $this->pozicePoznamky = ($pozice == 'top' ? 'top' : 'bottom');
        return $this;
    }

    /**
     * Draw borders around main sections
     * @param bool $draw - zapnutí/vypnutí orámování
     * @param float $width - tloušťka čáry
     * @param string $color - HEX barva
     * @param int $dash - šířka přerušení
     * @return mixed
     */
    public function setBorders(bool $draw, float $width = 0.1, string $color = '000000', int $dash = 0): self {
        $this->borders = array('enabled' => (bool)$draw, 'width' => $width, 'color' => $color, 'dash' => $dash);
        return $this;
    }

    /**
     * Vrátí pole s aktuálním nastavením orámování položek
     * @return array|array[]
     */
    public function getBorders(): array {
        if (!$this->borders['enabled']) {
            return array();
        }
        return array('B' => array('width' => $this->borders['width'], 'cap' => 'butt', 'dash' => $this->borders['dash'], 'color' => $this->getRGBColor($this->borders['color'])),
            'T' => array('width' => $this->borders['width'], 'cap' => 'butt', 'dash' => $this->borders['dash'], 'color' => $this->getRGBColor($this->borders['color'])),
            'L' => array('width' => $this->borders['width'], 'cap' => 'butt', 'dash' => $this->borders['dash'], 'color' => $this->getRGBColor($this->borders['color'])),
            'R' => array('width' => $this->borders['width'], 'cap' => 'butt', 'dash' => $this->borders['dash'], 'color' => $this->getRGBColor($this->borders['color'])));
    }

    /**
     * Naplni hodnoty pro EET
     * @param array $data s klici [fik], [bkp], [pkp], [rezim], [pokladna], [provozovna]
     * @return self
     */
    public function setEET(array $data): self {
        if (!is_array($data)) {
            return false;
        }
        $data['eet'] = true;
        $this->eet = $data;
        return $this;
    }

    /**
     * Nastavi zadavani castek, zda jsou pridavane polozky s DPH nebo bez DPH
     * @param boolean $val
     * @return self
     */
    public function setCastkySDPH(bool $val): self {
        $this->castkySDPH = (bool)$val;
        return $this;
    }

    /**
     * Čárový kód faktury. Vygeneruje čárový kód (code_128) a umístí jej na pozici X,Y. Velikost čárového kódu je dána parametry $sirka a $vyska.
     * @param int $kod
     * @param float $x
     * @param float $y
     * @param float $sirka
     * @param float $vyska
     * @return self
     */
    public function SetCarovyKod(int $kod, float $x = 10, float $y = 1, float $sirka = 30, float $vyska = 10): self {
        if ($kod < 1) {
            return $this;
        }
        $this->carovyKod['kod'] = (int)$kod;
        $this->carovyKod['x'] = (float)$x;
        $this->carovyKod['y'] = (float)$y;
        $this->carovyKod['sirka'] = (float)$sirka;
        $this->carovyKod['vyska'] = (float)$vyska;
        return $this;
    }

    /**
     * určuje doplňující informace k dokladům typu ODD/dobropis a STORNO..
     * @param string $puvodniDoklad - udává číslo původního dokladu
     * @param string $duvod - důvod opravy dokladu
     * @return self
     */
    public function SetDoplnujiciInformace(string $puvodniDoklad, string $duvod = ''): self {
        $this->doplnujiciInformace['puvodniDoklad'] = $puvodniDoklad;
        if ($duvod != '') {
            $this->doplnujiciInformace['duvod'] = $duvod;
        }
        return $this;
    }

    /**
     * Zapina/vypina rezim prenesene danove povinnosti
     * @param bool $val
     * @param string $text - pokud je predan prazdny retezec je pouzita predvolena, jinak tato predana
     * @return self
     */
    public function SetReverseCharge(bool $val, string $text = ''): self {
        $this->reverseCharge = $val;
        $this->reverseChargeText = $text;
        return $this;
    }

    /**
     * Nastaveni fontu pro texty. Defaultne je pouzit dejavusans. Pozor, font musi byt bud podporovan z operacnich systemu, nebo musi byt ve slozce tcpdf/fonts
     * @param string $fontName
     * @return self
     */
    public function SetFont(string $fontName): self {
        $this->font = $fontName;
        return $this;
    }

    /**
     * Vypsání QR platby
     * IBAN je automaticky generován z čísla účtu, v případě nemožnosti vygenerovat IBAN nebude QR kód vykreslen.
     * @param bool $vypisovat
     * @param float $x - X souřadnice na které bude QR kód vypsán
     * @param float $y - Y souřadnice na které bude QR kód vypsán
     * @param string $umisteni - 'F' = první strana, 'L' = poslední strana, 'PU' = platební údaje - Y souřadnice je pevně nastavena do sekce s platebními údaji, 'PA' = v patičce stránky za posledním vypsaným textem - v této pozici je velikost kód zphledňována pro celkovou výšku patičky a přechody na novou stránku.
     * @param float $velikost - velikost strany QR kódu, doporučujeme tuto velikost nastavit na hodnotu cca 30 a více pro lepší čitelnost, v případě malého rozměru kódu nelze zaručit jeho úspěšné načtení všemi zařízeními.
     * @param int $styl - 1 = orámovaný kód s popisem pod kódem, 2 = bez orámování s popisem napravo od kódu, 3 = bez orámování s popisem pod kódem.
     * @return self
     */
    public function SetQRPlatba(bool $vypisovat, float $x = 50, float $y = 50, string $umisteni = 'F', float $velikost = 20, int $styl = 1): self {
        $this->qr_platba['vypisovat'] = (bool)$vypisovat;
        $this->qr_platba['x'] = (float)$x;
        $this->qr_platba['y'] = $y;
        $this->qr_platba['velikost'] = (float)$velikost;
        $this->qr_platba['styl'] = (int)$styl;
        if ($umisteni == 'PA' || $umisteni == 'PU' || $umisteni == 'F' || $umisteni == 'L') {
            $this->qr_platba['strana'] = $umisteni;
        }
        return $this;
    }

    /**
     * Nastavení stylu
     * @param string $style - styl, který chceme nastavit ('fillColor' = barva pozadí rámečků hlavních nadpisů, 'fontColor' = barva písma hlavích nadpisů, 'priceFillColor' = barva pozadí rámečku u celkové ceny, 'priceFontColor' = barva písma u celkové ceny, 'itemFillColor' = barva podbarvení sudých položek, 'itemFontColor' = barva písma sudých položek)
     * @param mixed $value - pole s RGB barvami, nebo string HTML barvy (např. FFBBEE), itemFillColor připouští také parametr (bool)false
     * @return self
     */
    public function SetStyle(string $style, mixed $value): self {
        if (array_key_exists($style, $this->style)) {
            if (is_array($value)) {
                $this->style[$style] = $value;
            } else {
                if (is_bool($value) && $style == 'itemFillColor') {
                    $this->style[$style] = $value;
                } else {
                    $this->style[$style] = $this->getRGBColor($value);
                }
            }
        }
        return $this;
    }

    /**
     * Vypisovat konečného příjemce.
     * @param bool $value
     * @return self
     */
    public function SetKonecnyPrijemceVypisovat(bool $value): self {
        $this->konecnyPrijemce['vypisovat'] = $value;
        return $this;
    }

    /**
     * Adresa konečného příjemce je odlišná od adresy dodavatele?
     * @param bool $value
     * @return self
     */
    public function SetKonecnyPrijemceOdlisnaAdresa(bool $value): self {
        $this->konecnyPrijemce['jina_adresa'] = $value;
        return $this;
    }

    /**
     * Nastvení jazyka dokladu. Jazykové překlady musejí existovat ve složce langs.
     * @param string $value
     * @return self
     */
    public function SetJazyk(string $value): self {
        $fileName = str_replace(array('.', '/', '\\'), array('', '', ''), trim($value)) . '.php';
        if (file_exists(dirname(__FILE__) . '/langs/' . $fileName)) {
            include dirname(__FILE__) . '/langs/' . $fileName;
            WFPf_preklad::$preklady = $WFPf_lng;
        } else {
            $value = 'cs';
            $fileName = str_replace(array('.', '/', '\\'), array('', '', ''), trim($value)) . '.php';
            if (file_exists(dirname(__FILE__) . '/langs/' . $fileName)) {
                include dirname(__FILE__) . '/langs/' . $fileName;
                WFPf_preklad::$preklady = $WFPf_lng;
            } else {
                die('Nepodarilo se nahrat soubor s preklady - ' . $fileName);
            }
        }
        return $this;
    }

    /**
     * rčuje typ dokladu.
     * @param string $typDokladu - 1 = faktura, 2 = proforma, 3 = opravný daňový doklad / dobropis, 4 = storno faktura. Také je možné zadávat typy slovně (faktura, proforma, odd, storno)
     * @return self
     */
    public function SetTypDokladu(string $typDokladu): self {
        // 1 = faktura
        // 2 = proforma
        // 3 = ODD / dobropis
        $povoleneTypy = array(1 => 'faktura', 2 => 'proforma', 3 => 'odd', 4 => 'storno');
        if (isset($povoleneTypy[(int)$typDokladu])) {
            $this->typDokladu = (int)$typDokladu;
        } else {
            $typ = array_search($typDokladu, $povoleneTypy);
            if ($typ !== false) {
                $this->typDokladu = (int)$typ;
            }
        }
        return $this;
    }


    /**
     * Zadání elektronického podpisu k podpisu dokumentu
     * @param string $certifikat - Cesta k souboru certifikátu (*.crt), nebo přímo samotný řetězec obsahující certifikát. Certifikát musí být ve formátu PEM
     * @return self
     */
    public function SetPodpis(string $certifikat): self {
        if (file_exists($certifikat)) {
            $this->podpis = file_get_contents($certifikat);
        } else {
            $this->podpis = $certifikat;
        }
        return $this;
    }

    /**
     * Heslo k certifikátu
     * @param string $password
     * @return self
     */
    public function SetPodpisHeslo(string $password): self {
        $this->podpis_heslo = $password;
        return $this;
    }

    /**
     * Zadání doplňujících informací k podpisu
     * @param string $name
     * @param string $loc
     * @param string $reason
     * @param string $contact
     * @return self
     */
    public function SetPodpisInfo(string $name = '', string $loc = '', string $reason = '', string $contact = ''): self {
        $this->podpis_info['Name'] = $name;
        $this->podpis_info['Location'] = $loc;
        $this->podpis_info['Reason'] = $reason;
        $this->podpis_info['ContactInfo'] = $contact;
        return $this;
    }


    /**
     * Vrátí textový název dané sazby DPH, nebo null pokud neexistuje
     * @param float $sazba
     * @return string|null
     */
    public function GetSazba(float $sazba): ?string {
        $arr = array_keys($this->sazbyDPH);
        if (isset($arr[$sazba])) {
            return $arr[$sazba];
        }
        return null;
    }

    /**
     * Určuje, zda vypisovat ve shrnutí DPH i položky v sazbách s nulovým základem
     * @param bool $value
     * @return self
     */
    public function SetShrnutiPrazdne(bool $value): self {
        $this->shrnuti_prazdne = $value;
        return $this;
    }

    /**
     * Zadání slevy
     * @param float $sleva - hodnota slevy
     * @param int $typSlevy - 0 = sleva v konkrétní částce | 1 = sleva v procentech
     * @param int $mistoVypsani - místo vypsání slevy: 0 = mezi položkami | 1 = u celkové ceny | 2 = mezi položkami i u celkové ceny
     * @param int $vypsatINulove - 1 = vypsat i při nulové slevě | 0 = vypsat jen při nenulové slevě
     * @param float $dph - Sazba DPH, do které příjde sleva rozpočítat
     * @return self
     */
    public function SetSleva(float $sleva, int $typSlevy = 0, int $mistoVypsani = 1, int $vypsatINulove = 0, float $dph = 0): self {
        $this->sleva[0] = $sleva;
        $this->sleva[1] = $typSlevy;
        $this->sleva[2] = $mistoVypsani;
        $this->sleva[3] = $vypsatINulove;
        $this->sleva[4] = $dph;
        return $this;
    }

    /**
     * Nastavení zaokrouhlení - viz hint. k jednotlivým parametrům
     * @param int $value : 0 = nezaokrouhlovat, 1 = zaokrouhlovat na koruny, 2 = zaokrouhlovat na padesatniky
     * @param int $typ : def 1 : 1 = v polozkach uvadet zaokrouhlena cisla, ale pocitat s nezaokrouhlenymi, 2 = v polozkach uvadet zaokrouhlena cisla a pocitat s temito zaokrouhlenymi cisly, 3 = v polozkach uvadet zaokrouhlena cisla a pocitat s temito zaokrouhlenymi cisly, zaokrouhlit celkovou castku
     * @param int $zpusob : def 1 : zpusob zaokrouhleni 1 = matematicky, 2 = nahoru, 3 = dolu
     * @param int $rozpusteni : def 1 : urcuje do ktere sazby rozpocitat zaokrouhleni 1 = nejvyssi na dokladu, 2 = nejnizssi na dokladu, 3 = do sazby dle nejvyssi celkove castky na dokladu, 4 = do nulove sazby
     * @param bool $pocitatJakoPolozku : def true : urcuje zda pocitat zaokrouhleni jako polozku faktury
     * @return self
     */
    public function SetZaokrouhleni(int $value, int $typ = 1, int $zpusob = 1, int $rozpusteni = 1, bool $pocitatJakoPolozku = true): self {
        if ($value == 2) {
            $this->zaokrouhlit = 2;
        } elseif ($value == 0) {
            $this->zaokrouhlovat = 2;
            $this->zaokrouhlit = 1;
        } else {
            $this->zaokrouhlit = 1;
        }
        $this->typZaokrouhleni = $typ; // typ 1 = v polozkach uvadet zaokrouhlena cisla, ale pocitat s nezaokrouhlenymi
        // typ 2 = v polozkach uvadet zaokrouhlena cisla a pocitat s temito zaokrouhlenymi cisly
        // typ 3 = v polozkach uvadet zaokrouhlena cisla a pocitat s temito zaokrouhlenymi cisly, zaokrouhlit celkovou castku

        $this->zpusobZaokrouhleni = $zpusob; // zpusob 1 = matematicke zaokrouhleni
        // zpusob 2 = zaokrouhlovat nahoru
        // zpusob 3 = zaokrouhlovat dolu

        if ($rozpusteni == 1) {
            // rozpusteni do nejvyssi sazby na dokladu
            $this->zaokrouhleniRozpusteni = 1;
        } elseif ($rozpusteni == 2) {
            // rozpusteni do nejnizssi sazby na dokladu
            $this->zaokrouhleniRozpusteni = 2;
        } elseif ($rozpusteni == 3) {
            // rozpusteni do sazby s nejvyssi celkovou canou na dokladu
            $this->zaokrouhleniRozpusteni = 3;
        } elseif ($rozpusteni == 4) {
            // rozpusteni do nulove sazby
            $this->zaokrouhleniRozpusteni = 4;
        }
        return $this;
    }

    /**
     * Nastavení sazeb DPH
     * @param array $value array('0' => 'Nulová sazba', '12' => 'Snížená sazba', '21' => 'Základní sazba');
     * @return self
     */
    public function SetSazbyDPH(array $value) {
        if (is_array($value)) {
            $this->sazbyDPH = $value;
        }
        return $this;
    }

    /**
     * Povolení/zakázání výpisu množstevních sloupců a to m.j., cena za m.j. a počet kusů
     * @param bool $value
     * @return self
     */
    public function SetZobrazovatMJ(bool $value): self {
        $this->zobrazeneSloupce = array('mj' => $value, 'pocetmj' => $value, 'cenamj' => $value);
        return $this;
    }

    /**
     * Povolení zobrazení konkrétních sloupců u výpisu položek
     * @param bool $mj
     * @param bool $pocetMj
     * @param bool $cenaMj
     * @return self
     */
    public function SetZobrazeneSloupce(bool $mj, bool $pocetMj, bool $cenaMj): self {
        $this->zobrazeneSloupce = array('mj' => $mj, 'pocetmj' => $pocetMj, 'cenamj' => $cenaMj);
        return $this;
    }

    /**
     * @param type $value
     * @return self
     * @deprecated Funkce je od verze 2.2.001 zrusena, zustava zde pouze kvuli zpetne kompatibilite.
     */
    public function SetZaokrouhlovatDPH($value): self {
        return $this;
    }

    /**
     * zobrazení shrnutí DPH
     * @param bool $value
     * @return self
     */
    public function SetShrnutiDPH(bool $value): self {
        $this->shrnutiDPH = $value;
        return $this;
    }

    /**
     * Vypnutí / zapnutí odeslání vygenerované faktury emailem. Fakturu lze emailem zaslat jen když je SetOutputType nastaveno na F nebo FI
     * @param bool $value
     * @return self
     */
    public function SetSendEmail(bool $value): self {
        $this->send_to_mail = $value;
        return $this;
    }

    /**
     * Vypnutí / zapnutí odeslání vygenerované faktury emailem. Fakturu lze emailem zaslat jen když je SetOutputType nastaveno na F nebo FI
     * @param bool $value
     * @return self
     * @deprecated Použijte metodu SetSendEmail
     */
    public function SetSend_email(bool $value): self {
        return $this->SetSendEmail($value);
    }

    /**
     * Vykreslí obrázek na zadanou pozici
     * @param string $cesta - cesta k obrázku, akceptovány jsou jpg, png, gif
     * @param float $horizontal - horizontální pozice
     * @param float|string $vertical - vertikální pozice - pokud do hodnoty $vertical zadáme text začínající písmenem C a následovaný znakem + nebo - a číslem, tak bude jako vertikální pozice použita pozice podpisové čáry +/- dané číslo (příklad: zadání 'c-15' znaméná 15mm před podpisovou čarou), toto funguje pouze při nastavení opakování na poslední stranu ('L').
     * @param string $opakovat - opakování: F=první strana | L=poslední strana | A=všechny
     * @param float|null $sirka
     * @param float|null $vyska
     * @return self
     */
    public function SetObrazek(string $cesta, float $horizontal, float|string $vertical, string $opakovat = 'F', ?float $sirka = NULL, ?float $vyska = NULL): self {
        if ($cesta != '' && isset($horizontal) && isset($vertical)) {
            $data = array();
            $data[0] = trim($cesta);
            $data[1] = floatval($horizontal);
            $data[2] = $vertical;
            switch (strtolower($opakovat)) {
                case 'l':
                    $data[3] = 'L';
                    break;
                case 'a':
                    $data[3] = 'A';
                    break;
                default:
                    $data[3] = 'F';
                    break;
            }

            $stop = false;
            if (!isset($sirka) || !isset($vyska)) {
                $path_parts = pathinfo($cesta);
                switch (strtolower($path_parts["extension"])) {
                    case 'jpg' :
                    case 'jpeg':
                        $im = imagecreatefromjpeg($cesta);
                        break;
                    case 'gif' :
                        $im = imagecreatefromgif($cesta);
                        break;
                    case 'png' :
                        $im = imagecreatefrompng($cesta);
                        break;
                    default :
                        $stop = true;
                        break;
                }
                if (!$stop) {
                    $data[4] = floatval(imagesx($im) / 3.78);
                    $data[5] = floatval(imagesy($im) / 3.78);
                    imagedestroy($im);
                }
            } else {
                $data[4] = floatval($sirka);
                $data[5] = floatval($vyska);
            }
            $this->obrazek[] = $data;
        }
        return $this;
    }

    /**
     * @deprecated
     * @deprecated 2.3.001
     * @deprecated Use method SetTextUPodpisu
     */
    public function SetText_u_podpisu(string $text, float $velikost = 8, string $styl = ''): self {
        $this->SetTextUPodpisu($text, $velikost, $styl);
        return $this;
    }

    /**
     * Set text which is printed next to signature line.
     * @param string $text
     * @param float $velikost
     * @param string $styl
     * @return self
     */
    public function SetTextUPodpisu(string $text, float $velikost = 8, string $styl = ''): self {
        $this->text_podpis[0] = $text;
        $this->text_podpis[1] = $velikost;
        $this->text_podpis[2] = $styl;
        return $this;
    }

    /**
     * @deprecated
     * @deprecated 2.3.001
     * @deprecated Use method SetTextKonec
     */
    public function SetText_konec($value1, $value2 = 8, $value3 = '') {
        $this->SetTextKonec($value1, $value2, $value3);
    }

    /**
     * Set text which is printed at the end of the invoice.
     * @param string $text
     * @param float $fontHeight
     * @param string $style - font style B for bold, I for italic
     * @return self
     */
    public function SetTextKonec(string $text, float $fontHeight = 8, string $style = ''): self {
        $this->text_konec[0] = $text;
        $this->text_konec[1] = $fontHeight;
        $this->text_konec[2] = $style;
        return $this;
    }

    /**
     * @deprecated
     * @deprecated 2.3.001
     * @deprecated Use method SetCisloFaktury
     */
    public function SetCislo_faktury($value): self {
        return $this->SetCisloFaktury($value);
    }

    /**
     * Set invoice number.
     * @param string $value
     * @return self
     */
    public function SetCisloFaktury(string $value): self {
        $this->cislo_faktury = $value;
        return $this;
    }

    /**
     * @deprecated
     * @deprecated 2.3.001
     * @deprecated Use method SetUhrazenoZalohou
     */
    public function SetUhrazeno_zalohou($value): self {
        return $this->SetUhrazenoZalohou($value);
    }

    /**
     * Set how much has already been paid by the deposit.
     * @param float $uhrazenaZaloha
     * @return self
     */
    public function SetUhrazenoZalohou(float $uhrazenaZaloha): self {
        $this->zalohy = $uhrazenaZaloha;
        return $this;
    }

    /**
     * @param $value
     * @return self
     * @deprecated Use method SetVzdalenostPolozek
     * @deprecated
     * @deprecated 2.3.001
     */
    public function SetVzdalenost_polozek($value): self {
        $this->SetVzdalenostPolozek($value);
        return $this;
    }

    /**
     * Set margin between invoice items.
     * @param float $vzdalenost
     * @return self
     */
    public function SetVzdalenostPolozek(float $vzdalenost): self {
        $this->vzdalenost_polozek = $vzdalenost;
        return $this;
    }

    /**
     * Nastavit barvu podtržení položek ($value = false = nepodtrhávat)
     * @param array|bool $value array(0-255,0-255,0-255) | false
     * @return self
     */
    public function SetPodtrzeni(array|bool $value): self {
        if (is_array($value)) {
            $this->podtrzeni = $value;
        } else {
            if (is_bool($value)) {
                $this->podtrzeni = $value;
            } else {
                $this->podtrzeni = $this->getRGBColor($value);
            }
        }
        return $this;
    }

    /**
     * Nastavit barvu podbarvení sudých položek (false=nepodbarvovat)
     * @param array|bool $value
     * @return self
     * @deprecated Funkci nahrazuje nová funkce SetStyle
     */
    public function SetFillcolor(array|bool $value): self {

        if (is_array($value)) {
            $this->style['itemFillColor'] = $value;
        } else {
            if (is_bool($value)) {
                $this->style['itemFillColor'] = $value;
            } else {
                $this->style['itemFillColor'] = $this->getRGBColor($value);
            }
        }
        return $this;
    }

    /**
     * Určuje zda má být faktura vystavena v režimu plátce DPH
     * @param bool $value
     * @return self
     */
    public function SetPlatceDPH(bool $value): self {
        $this->platceDPH = $value;
        return $this;
    }

    /**
     * Nastavení autora PDF dokumentu
     * @param string $autor
     * @return self
     */
    public function SetAutor(string $autor): self {
        $this->autor = $autor;
        return $this;
    }

    /**
     * Nastaví titulek PDF dokumentu
     * @param string $titulek
     * @return self
     */
    public function SetTitulek(string $titulek): self {
        $this->titulek = $titulek;
        return $this;
    }

    /**
     * Měna dokladu
     * @param string $mena
     * @return self
     */
    public function SetMena(string $mena): self {
        $this->mena = $mena;
        return $this;
    }

    /**
     * Nastavi jmeno nazev pro stazeni a akci output option na F
     * Deprecated since 2.1.004 - pouzijte primo metodu SetNazevSouboru a SetOutputType
     * @param string $value
     * @return self
     */
    public function SetJmenosouboru(string $value): self {
        $this->jmenosouboru = $value;
        $this->outputType = 'F';
        return $this;
    }

    /**
     * Název souboru pro uložení. Použije se pro uložení na server a také v interaktivním zobrazení když dá uživatel uložit lokálně.
     * @param string $nazevSouboru
     * @return self
     */
    public function SetNazevSouboru(string $nazevSouboru): self {
        $this->jmenosouboru = $nazevSouboru;
        return $this;
    }

    /**
     * Určuje výstupní možnosti
     * @param string $typ D = force download, I = interaktivní zobrazení v prohlížeči (pokud je možné), F = uložení na server, FI = kombinace F a I, FD = kombinace F a D, E = vrátí výstup jako base64 mime multi-part email attachment (RFC 2045).
     * @return self
     */
    public function SetOutputType(string $typ): self {
        $povoleneTypy = array('D', 'I', 'F', 'FI', 'FD', 'E', 'S');
        if (in_array($typ, $povoleneTypy)) {
            $this->outputType = $typ;
        }
        return $this;
    }

}

class WFPf_preklad {

    public static ?array $preklady = null;

    /**
     * Přeloží daný text do načteného jazyka
     * @param string $string
     * @return string
     */
    public static function t(string $string): string {
        if (isset(self::$preklady[$string])) {
            return self::$preklady[$string];
        } else {
            return $string;
        }
    }

    /**
     * Test zda je jazyk načten
     * @return bool
     */
    public static function jeJazyk(): bool {
        return !is_null(self::$preklady);
    }

}

/* * *********** TRIDA PRO GENEROVANI IBAN KODU Z CISLA UCTU A QR STRINGU ***************** */

class WFPf_iban {

    private static $accounPrefix;
    private static $acount;
    private static $bankCode;
    private static $bic;
    private static $errors = array();

    private static function setError($e) {
        self::$errors[] = $e;
    }

    private static function getErrors() {
        return self::$errors;
    }

    private static function displayErrors() {
        foreach (self::getErrors() as $e) {
            echo $e . '<br>';
        }
    }

    private static function stripSpaces($txt) {
        return str_replace(' ', '', $txt ?? '');
    }

    private static function testNum($num) {
        $ret = "N"; //N-cislo0,C-cislo,E-chyba
        $vahy = array("1", "2", "4", "8", "5", "10", "9", "7", "3", "6");
        $suma = 0;
        $len = strlen($num);
        $j = 0;
        for ($i = $len - 1; $i >= 0; $i--) {
            $c = substr($num ?? '', $i, 1);
            if ($c != '0') {
                if ($c >= 1 && $c <= 9) {
                    $ret = "C";
                    $suma = $suma + $c * $vahy[$j];
                } else {
                    $ret = "E";
                    break;
                }
            }
            $j++;
        }
        if (($suma % 11) == 0) {
            $ret = $ret . "M";
        } else {
            $ret = $ret . "E";
        };

        return $ret;
    }

    private static function getPrefix($pu) {
        self::$accounPrefix = "000000" . self::stripSpaces($pu);
        self::$accounPrefix = substr(self::$accounPrefix, -6);

        $sts = self::testNum(self::$accounPrefix);
        if (substr($sts, 0, 1) == "E") {
            self::setError('První část čísla účtu není numerická');
            return false;
        }
        if (substr($sts, 1, 1) == 'E') {
            self::setError('Chybné číslo účtu');
            return false;
        }
        return true;
    }

    private static function getAccount($cu) {
        self::$acount = "0000000000" . self::stripSpaces($cu);
        self::$acount = substr(self::$acount, -10);

        $sts = self::testNum(self::$acount);
        if (substr($sts, 0, 1) == "N") {
            self::setError('Druhá část čísla účtu je nulová');
            return false;
        }
        if (substr($sts, 0, 1) == "E") {
            self::setError('Druhá část čísla účtu není numerická');
            return false;
        }
        if (substr($sts, 1, 1) == 'E') {
            self::setError('Chybné číslo účtu');
            return false;
        }

        return true;
    }

    private static function getBank($bankCode, $onlyValidBic = false) {
        self::$bankCode = "0000" . self::stripSpaces($bankCode);
        self::$bankCode = substr(self::$bankCode, -4);

        self::$bic = self::getBic(self::$bankCode);

        if (self::$bic == '' && $onlyValidBic) {
            self::setError('Chybný kód banky');
            return false;
        }
        return true;
    }

    private static function getBic($bnk) {
        $bnkbic = array();
        $bnkbic[0] = "0100KOMBCZPP";
        $bnkbic[1] = "0300CEKOCZPP";
        $bnkbic[2] = "0600AGBACZPP";
        $bnkbic[3] = "0710CNBACZPP";
        $bnkbic[4] = "0800GIBACZPX";
        $bnkbic[5] = "2010FIOBCZPP";
        $bnkbic[6] = "2020BOTKCZPP";
        $bnkbic[7] = "2030?       ";
        $bnkbic[8] = "2050?       ";
        $bnkbic[9] = "2060CITFCZPP";
        $bnkbic[10] = "2070MPUBCZPP";
        $bnkbic[11] = "2100?       ";
        $bnkbic[12] = "2200?       ";
        $bnkbic[13] = "2210FICHCZPP";
        $bnkbic[14] = "2220?       ";
        $bnkbic[15] = "2230?       ";
        $bnkbic[16] = "2240POBNCZPP";
        $bnkbic[17] = "2250?       ";
        $bnkbic[18] = "2310ZUNOCZPP";
        $bnkbic[19] = "2600CITICZPX";
        $bnkbic[20] = "2700BACXCZPP";
        $bnkbic[21] = "3030AIRACZP1";
        $bnkbic[22] = "3500INGBCZPP";
        $bnkbic[23] = "4000SOLACZPP";
        $bnkbic[24] = "4300CMZRCZP1";
        $bnkbic[25] = "5400ABNACZPP";
        $bnkbic[26] = "5500RZBCCZPP";
        $bnkbic[27] = "5800JTBPCZPP";
        $bnkbic[28] = "6000PMBPCZPP";
        $bnkbic[29] = "6100EQBKCZPP";
        $bnkbic[30] = "6200COBACZPX";
        $bnkbic[31] = "6210BREXCZPP";
        $bnkbic[32] = "6300GEBACZPP";
        $bnkbic[33] = "6700SUBACZPP";
        $bnkbic[34] = "6800VBOECZ2X";
        $bnkbic[35] = "7910DEUTCZPX";
        $bnkbic[36] = "7940SPWTCZ21";
        $bnkbic[37] = "7950?       ";
        $bnkbic[38] = "7960?       ";
        $bnkbic[39] = "7970?       ";
        $bnkbic[40] = "7980?       ";
        $bnkbic[41] = "7990?       ";
        $bnkbic[42] = "8030GENOCZ21";
        $bnkbic[43] = "8040OBKLCZ2X";
        $bnkbic[44] = "8060?       ";
        $bnkbic[45] = "8090CZEECZPP";
        $bnkbic[46] = "8150MIDLCZPP";
        $bnkbic[47] = "8200?       ";

        $bic = '';
        foreach ($bnkbic as $bnbc) {
            if ($bnk == substr($bnbc, 0, 4)) {
                $bic = substr($bnbc, 4, 8);
                continue;
            }
        }
        return $bic;
    }

    private static function calc($buf) {
        $index = 0;
        $pz = -1;
        while ($index <= strlen($buf)) {
            if ($pz < 0) {
                $dividend = substr($buf, $index, 9);
                $index += 9;
            } else if ($pz >= 0 && $pz <= 9) {
                $dividend = $pz . substr($buf, $index, 8);
                $index += 8;
            } else {
                $dividend = $pz . substr($buf, $index, 7);
                $index += 7;
            }
            $pz = $dividend % 97;
        }
        return $pz;
    }

    /**
     * Ziska hodnotu IBAN dle prefixu uctu, cisla uctu a kodu banky
     *
     * @param int $account : cislo uctu - bez predcisli a kodu banky
     * @param int $bankCode : 4 mistny kod banky
     * @param int [$accountPrefix]: predcisli uctu
     * @param boolean [$onlyValidBic]: Pokud je true, tak vrati IBAN, jen pokud je kod banky v uvedenych BIC kodech ceskych bank
     * @return string|boolean: v pripade chyby vraci false, jinak IBAN format cisla uctu
     * @since 1.0.0.000
     * @access public
     * */
    public static function getIban($account, $bankCode, $accountPrefix = '', $onlyValidBic = false) {
        $isError = false;
        $ib = '';

        if (strpos($account ?? '', '-') !== false) {
            $accountPrefix = substr($account, 0, strpos($account, '-'));
            $account = substr($account, strpos($account, '-') + 1);
        }

        if (!self::getPrefix($accountPrefix)) {
            $isError = true;
        }

        if (!self::getAccount($account)) {
            $isError = true;
        }

        if (!self::getBank($bankCode, $onlyValidBic)) {
            $isError = true;
        }

        if ($isError) {
            //self::displayErrors();
            return false;
        }

        $di = self::calc(self::$bankCode . self::$accounPrefix . self::$acount . "123500");
        $di = 98 - $di;
        if ($di < 10) {
            $di = "0" . $di;
        }
        $ib = "CZ" . $di . self::$bankCode . self::$accounPrefix . self::$acount;
        $ib = substr($ib, 0, 4) . " " . substr($ib, 4, 4) . " " . substr($ib, 8, 4) . " " . substr($ib, 12, 4) . " " . substr($ib, 16, 4) . " " . substr($ib, 20, 4);

        return $ib;
    }

    public static function getSwift($bankCode) {
        if (!self::getBank($bankCode, true)) {
            //self::displayErrors();
            return false;
        }
        return self::$bic;
    }

    /**
     * Ziska hodnotu QR Stringu potrebnou pro vygenerovani QR kodu
     *
     * @param array $data : Pole hodnot s povinnymi klici: iban, amountbic a volitelnymu klici vs, ks, ss
     * @return string|boolean: v pripade chyby vraci false, jinak QR String
     * @since 1.0.0.000
     * @access public
     * */
    public static function getQRString($data = array('iban' => '', 'bic' => '', 'amount' => '', 'vs' => '', 'ks' => '', 'ss' => '')) {
        if (!isset($data['iban']) || !isset($data['amount'])) {
            return false;
        }
        if ($data['iban'] == '' || $data['amount'] == '') {
            return false;
        }

        $qrs = 'SPD*1.0*ACC:' . str_replace(' ', '', $data['iban']);

        if (isset($data['bic']) && $data['bic'] != '') {
            $qrs .= '+' . $data['bic'] . '*';
        }

        $qrs .= 'AM:' . round((float)$data['amount'], 2) . '*';

        if (isset($data['vs']) && $data['vs'] != '') {
            $qrs .= 'X-VS:' . $data['vs'] . '*';
        }

        if (isset($data['ks']) && $data['ks'] != '') {
            $qrs .= 'X-KS:' . $data['ks'] . '*';
        }

        if (isset($data['ss']) && $data['ss'] != '') {
            $qrs .= 'X-SS:' . $data['ss'] . '*';
        }
        return trim($qrs, '*');
    }

}