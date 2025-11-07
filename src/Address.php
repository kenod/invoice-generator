<?php

declare(strict_types=1);

namespace Danek\InvoiceGenerator;

/**
 * Represents an address (supplier, customer, or final recipient)
 */
class Address
{
    /**
     * Company name
     */
    public string $company = '';

    /**
     * Contact person name
     */
    public string $name = '';

    /**
     * Street address
     */
    public string $street = '';

    /**
     * Postal code
     */
    public string $postalCode = '';

    /**
     * City
     */
    public string $city = '';

    /**
     * Country
     */
    public string $country = 'Czech Republic';

    /**
     * Separator between company and name (if both present)
     */
    public string $separator = '--';

    /**
     * Company ID number (IČ in Czech)
     */
    public string $companyId = '';

    /**
     * Tax ID number (DIČ in Czech)
     */
    public string $taxId = '';

    /**
     * Separator between IDs
     */
    public string $separator2 = '--';

    /**
     * Phone number
     */
    public string $phone = '';

    /**
     * Email address
     */
    public string $email = '';

    /**
     * Website URL
     */
    public string $web = '';

    /**
     * Translates the country name to the loaded language
     *
     * @return self
     */
    public function translate(): self
    {
        $this->country = Translation::t($this->country);
        return $this;
    }

    /**
     * Gets non-empty properties of this address
     *
     * @return array<string, string>
     */
    public function getProperties(): array
    {
        $properties = [];
        foreach ($this as $key => $value) {
            if ($value === '') {
                continue;
            }
            $properties[$key] = $value;
        }

        return $properties;
    }

    /**
     * Sets company name
     *
     * @param string $company Company name
     * @return self
     */
    public function setCompany(string $company): self
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Sets country
     *
     * @param string $country Country name
     * @return self
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Sets phone number
     *
     * @param string $phone Phone number
     * @return self
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Sets email address
     *
     * @param string $email Email address
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Sets website URL
     *
     * @param string $web Website URL
     * @return self
     */
    public function setWeb(string $web): self
    {
        $this->web = $web;
        return $this;
    }

    /**
     * Sets contact person name
     *
     * @param string $name Contact name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets street address
     *
     * @param string $street Street address
     * @return self
     */
    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    /**
     * Sets postal code
     *
     * @param string $postalCode Postal code
     * @return self
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Sets city
     *
     * @param string $city City name
     * @return self
     */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Sets company ID (IČ)
     *
     * @param string $companyId Company ID
     * @return self
     */
    public function setCompanyId(string $companyId): self
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * Sets tax ID (DIČ)
     *
     * @param string $taxId Tax ID
     * @return self
     */
    public function setTaxId(string $taxId): self
    {
        $this->taxId = $taxId;
        return $this;
    }
}
