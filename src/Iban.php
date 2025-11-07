<?php

declare(strict_types=1);

namespace Danek\InvoiceGenerator;

/**
 * IBAN and QR payment code generator for Czech bank accounts
 */
class Iban
{
    private static ?string $accountPrefix = null;

    private static ?string $account = null;

    private static ?string $bankCode = null;

    private static ?string $bic = null;

    /**
     * @var list<string>
     */
    private static array $errors = [];

    private static function setError(string $error): void
    {
        self::$errors[] = $error;
    }

    /**
     * @return list<string>
     */
    private static function getErrors(): array
    {
        return self::$errors;
    }

    private static function displayErrors(): void
    {
        foreach (self::getErrors() as $error) {
            echo $error . '<br>';
        }
    }

    private static function stripSpaces(?string $text): string
    {
        return str_replace(' ', '', $text ?? '');
    }

    /**
     * Validates Czech account number using checksum
     *
     * @param string $number Account number to validate
     * @return string Status code: N=zero, C=valid, E=error, M=modulo ok
     */
    private static function testNumber(string $number): string
    {
        $status = 'N'; // N=zero, C=number, E=error
        $weights = [1, 2, 4, 8, 5, 10, 9, 7, 3, 6];
        $sum = 0;
        $length = strlen($number);
        $j = 0;

        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = substr($number, $i, 1);
            if ($digit !== '0') {
                if ($digit >= '1' && $digit <= '9') {
                    $status = 'C';
                    $sum += ((int) $digit) * $weights[$j];
                } else {
                    $status = 'E';
                    break;
                }
            }
            $j++;
        }

        if (($sum % 11) === 0) {
            $status .= 'M';
        } else {
            $status .= 'E';
        }

        return $status;
    }

    private static function getPrefix(string $prefix): bool
    {
        self::$accountPrefix = '000000' . self::stripSpaces($prefix);
        self::$accountPrefix = substr(self::$accountPrefix, -6);

        $status = self::testNumber(self::$accountPrefix);
        if (str_starts_with($status, 'E')) {
            self::setError('First part of account number is not numeric');
            return false;
        }

        if (str_ends_with($status, 'E')) {
            self::setError('Invalid account number');
            return false;
        }

        return true;
    }

    private static function getAccount(string $accountNumber): bool
    {
        self::$account = '0000000000' . self::stripSpaces($accountNumber);
        self::$account = substr(self::$account, -10);

        $status = self::testNumber(self::$account);
        if (str_starts_with($status, 'N')) {
            self::setError('Second part of account number is zero');
            return false;
        }

        if (str_starts_with($status, 'E')) {
            self::setError('Second part of account number is not numeric');
            return false;
        }

        if (str_ends_with($status, 'E')) {
            self::setError('Invalid account number');
            return false;
        }

        return true;
    }

    private static function getBank(string $bankCode, bool $onlyValidBic = false): bool
    {
        self::$bankCode = '0000' . self::stripSpaces($bankCode);
        self::$bankCode = substr(self::$bankCode, -4);

        self::$bic = self::getBic(self::$bankCode);

        if (self::$bic === '' && $onlyValidBic) {
            self::setError('Invalid bank code');
            return false;
        }

        return true;
    }

    /**
     * Gets BIC (SWIFT) code for Czech bank
     *
     * @param string $bankCode 4-digit bank code
     * @return string BIC code or empty string if not found
     */
    private static function getBic(string $bankCode): string
    {
        // Czech banks BIC mapping
        $bankBicMap = [
            '0100' => 'KOMBCZPP',
            '0300' => 'CEKOCZPP',
            '0600' => 'AGBACZPP',
            '0710' => 'CNBACZPP',
            '0800' => 'GIBACZPX',
            '2010' => 'FIOBCZPP',
            '2020' => 'BOTKCZPP',
            '2060' => 'CITFCZPP',
            '2070' => 'MPUBCZPP',
            '2210' => 'FICHCZPP',
            '2240' => 'POBNCZPP',
            '2310' => 'ZUNOCZPP',
            '2600' => 'CITICZPX',
            '2700' => 'BACXCZPP',
            '3030' => 'AIRACZP1',
            '3500' => 'INGBCZPP',
            '4000' => 'SOLACZPP',
            '4300' => 'CMZRCZP1',
            '5400' => 'ABNACZPP',
            '5500' => 'RZBCCZPP',
            '5800' => 'JTBPCZPP',
            '6000' => 'PMBPCZPP',
            '6100' => 'EQBKCZPP',
            '6200' => 'COBACZPX',
            '6210' => 'BREXCZPP',
            '6300' => 'GEBACZPP',
            '6700' => 'SUBACZPP',
            '6800' => 'VBOECZ2X',
            '7910' => 'DEUTCZPX',
            '7940' => 'SPWTCZ21',
            '8030' => 'GENOCZ21',
            '8040' => 'OBKLCZ2X',
            '8090' => 'CZEECZPP',
            '8150' => 'MIDLCZPP',
        ];

        return $bankBicMap[$bankCode] ?? '';
    }

    /**
     * Calculates IBAN checksum using modulo 97
     *
     * @param string $buffer Input string for calculation
     * @return int Modulo 97 result
     */
    private static function calculateMod97(string $buffer): int
    {
        $index = 0;
        $remainder = -1;

        while ($index <= strlen($buffer)) {
            if ($remainder < 0) {
                $dividend = substr($buffer, $index, 9);
                $index += 9;
            } elseif ($remainder >= 0 && $remainder <= 9) {
                $dividend = $remainder . substr($buffer, $index, 8);
                $index += 8;
            } else {
                $dividend = $remainder . substr($buffer, $index, 7);
                $index += 7;
            }

            $remainder = ((int) $dividend) % 97;
        }

        return $remainder;
    }

    /**
     * Generates IBAN from Czech account number
     *
     * @param string|int $account Account number (without prefix and bank code)
     * @param string|int $bankCode 4-digit bank code
     * @param string|int $accountPrefix Account prefix (optional)
     * @param bool $onlyValidBic Return IBAN only if bank code has valid BIC
     * @return string|false IBAN format or false on error
     */
    public static function getIban(
        string|int $account,
        string|int $bankCode,
        string|int $accountPrefix = '',
        bool $onlyValidBic = false
    ): string|false {
        $isError = false;
        $account = (string) $account;
        $bankCode = (string) $bankCode;
        $accountPrefix = (string) $accountPrefix;

        // Handle account number with prefix (format: prefix-account)
        if (str_contains($account, '-')) {
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
            return false;
        }

        $checkDigits = self::calculateMod97(
            self::$bankCode . self::$accountPrefix . self::$account . '123500'
        );
        $checkDigits = 98 - $checkDigits;
        $checkDigitsFormatted = str_pad((string) $checkDigits, 2, '0', STR_PAD_LEFT);

        $iban = 'CZ' . $checkDigitsFormatted . self::$bankCode . self::$accountPrefix . self::$account;

        // Format with spaces
        return substr($iban, 0, 4) . ' ' .
               substr($iban, 4, 4) . ' ' .
               substr($iban, 8, 4) . ' ' .
               substr($iban, 12, 4) . ' ' .
               substr($iban, 16, 4) . ' ' .
               substr($iban, 20, 4);
    }

    /**
     * Gets SWIFT/BIC code for bank
     *
     * @param string|int $bankCode 4-digit bank code
     * @return string|false BIC code or false on error
     */
    public static function getSwift(string|int $bankCode): string|false
    {
        $bankCode = (string) $bankCode;

        if (!self::getBank($bankCode, true)) {
            return false;
        }

        return self::$bic;
    }

    /**
     * Generates QR payment string for Czech QR payments
     *
     * @param array{iban: string, bic?: string, amount: string|float, vs?: string, ks?: string, ss?: string} $data Payment data
     * @return string|false QR string or false on error
     */
    public static function getQRString(array $data): string|false
    {
        if (!isset($data['iban'], $data['amount'])) {
            return false;
        }

        if ($data['iban'] === '' || $data['amount'] === '') {
            return false;
        }

        $qrString = 'SPD*1.0*ACC:' . str_replace(' ', '', $data['iban']);

        if (isset($data['bic']) && $data['bic'] !== '') {
            $qrString .= '+' . $data['bic'];
        }

        $qrString .= '*AM:' . round((float) $data['amount'], 2) . '*';

        if (isset($data['vs']) && $data['vs'] !== '') {
            $qrString .= 'X-VS:' . $data['vs'] . '*';
        }

        if (isset($data['ks']) && $data['ks'] !== '') {
            $qrString .= 'X-KS:' . $data['ks'] . '*';
        }

        if (isset($data['ss']) && $data['ss'] !== '') {
            $qrString .= 'X-SS:' . $data['ss'] . '*';
        }

        return trim($qrString, '*');
    }
}
