# Příklady použití Kenod Invoice Generator

Tento adresář obsahuje příklady použití moderní verze knihovny `Kenod\InvoiceGenerator`.

## Přehled příkladů

### 01_basic_invoice.php
Jednoduchý příklad základní faktury:
- Minimální konfigurace
- Dodavatel a odběratel
- Platební údaje
- Několik položek s DPH
- Ideální jako výchozí bod pro vaše projekty

**Spustit:**
```bash
php 01_basic_invoice.php
```

### 02_eet.php
Příklad faktury s evidencí tržeb (EET):
- Integrace s EET systémem
- FIK, PKP, BKP kódy
- Režim běžný/zjednodušený
- Šedé barevné schéma

**Spustit:**
```bash
php 02_eet.php
```

### 03_reverse_charge.php
Faktura s přenesením daňové povinnosti (Reverse Charge):
- Vhodné pro EU transakce
- Bez kalkulace DPH
- Text "Daň odvede zákazník"

**Spustit:**
```bash
php 03_reverse_charge.php
```

### 04_translations.php
Příklad překladu faktury do slovenštiny:
- Slovenský jazyk (sk)
- Měna EUR
- Přeložené texty a popisky

**Spustit:**
```bash
php 04_translations.php
```

### 05_eco_print.php
Ekonomický tisk - minimální spotřeba inkoustu:
- Bílé pozadí
- Černé okraje
- Zobrazení sloupce MJ
- Rozšířené údaje dodavatele (web, email, telefon)
- Poznámky pod položkami

**Spustit:**
```bash
php 05_eco_print.php
```

### 06_images.php
Přidávání obrázků na fakturu:
- Logo na všech stránkách ('A')
- Vodoznak na poslední stránce ('L')
- Dodatečné logo na první stránce ('F')
- Různé pozice obrázků

**Spustit:**
```bash
php 06_images.php
```

### 07_custom_callback.php
Použití vlastní callback funkce:
- Přidání custom textu
- Přístup ke všem TCPDF metodám
- Vodoznaky, tvary, dodatečný obsah

**Spustit:**
```bash
php 07_custom_callback.php
```

### 08_english.php
Příklad faktury v anglickém jazyce:
- Anglická lokalizace (en)
- Mezinárodní faktura
- Všechny texty přeloženy do angličtiny

**Spustit:**
```bash
php 08_english.php
```

### 09_multiple_documents.php
Generování více dokumentů v jednom PDF:
- Kompletní faktura s EET údaji
- Druhá faktura s jinými údaji
- Dodací list (non-VAT dokument)
- Použití metody `clearData()` mezi dokumenty
- Pokročilé nastavení a styling

**Spustit:**
```bash
php 09_multiple_documents.php
```

### 10_credit_note.php
Opravný daňový doklad (Credit Note):
- Typ dokumentu: credit note/correction (`setDocumentType(3)`)
- Reference na původní fakturu (`setAdditionalInfo()`)
- Záporné částky pro vrácení peněz
- Čárový kód s číslem dokladu (`setBarcode()`)
- Vlastní popisky sazeb DPH (`setVatRates()`)
- Rozestupy mezi položkami (`setItemSpacing()`)
- Texty nad podpisem (`setSignatureText()`)
- Texty v patičce (`setFooterText()`)
- Použití konstantního a specifického symbolu
- Modré barevné schéma s ohraničením

**Spustit:**
```bash
php 10_credit_note.php
```

## Starší příklady (původní API)

Pro reference jsou k dispozici i starší příklady používající původní API:
- `generuj_fakturu.php` - komplexní příklad staré verze
- `eet.php` - EET s původním API
- `reverse_charge.php` - Reverse charge s původním API
- `preklady.php` - Překlady s původním API
- `ekonomicky_tisk.php` - Ekonomický tisk s původním API
- `obrazky.php` - Obrázky s původním API
- `custom_callback.php` - Custom callback s původním API
- `vice_dokladu.php` - Více dokumentů v jednom PDF

**Poznámka:** Tyto soubory používají starší namespace `\WFPfaktury\WFPfaktury` a nejsou kompatibilní s novou verzí.

## Migrace ze staré verze

Pro migraci ze staré verze na novou, viz soubor `MIGRATION.md` v kořenovém adresáři projektu.

Hlavní změny:
- Namespace: `\WFPfaktury\WFPfaktury` → `Kenod\InvoiceGenerator\InvoiceGenerator`
- Názvy metod: `SetJmeno()` → `setName()`, `SetCisloFaktury()` → `setInvoiceNumber()`, atd.
- Fluent interface: všechny settery vrací `$this` pro řetězení
- Typed properties: PHP 8.3+ strict types

## Spuštění všech příkladů

Pro otestování všech nových příkladů najednou:

```bash
php test_all_examples.php
```

## Dokumentace API

Kompletní dokumentace všech tříd a metod je k dispozici v kořenovém adresáři:
- `README.md` - Základní přehled a instalace
- `MIGRATION.md` - Průvodce migrací
- PhpDoc komentáře ve zdrojových souborech

## Podpora

Pro dotazy a hlášení chyb použijte GitHub Issues:
https://github.com/kenod/wfpfaktury/issues
