<?php

declare(strict_types=1);

namespace WFP\Invoice;

/**
 * Email configuration for sending invoices via email
 */
class Email
{
    /**
     * Recipient email address
     */
    public string $address = '';

    /**
     * Email body text
     */
    public string $body = '';

    /**
     * Email subject
     */
    public string $subject = '';

    /**
     * Sender email address
     */
    public string $from = '';

    /**
     * Sender name
     */
    public string $fromName = '';

    /**
     * Path to PHPMailer class file (if needed for custom setup)
     */
    public string $phpMailerPath = '';

    /**
     * Sets recipient email address
     *
     * @param string $email Recipient email
     * @return self
     */
    public function setAddress(string $email): self
    {
        $this->address = $email;
        return $this;
    }

    /**
     * Sets email body text
     *
     * @param string $body Email body
     * @return self
     */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Sets email subject
     *
     * @param string $subject Email subject
     * @return self
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Sets sender email address
     *
     * @param string $emailFrom Sender email
     * @return self
     */
    public function setFrom(string $emailFrom): self
    {
        $this->from = $emailFrom;
        return $this;
    }

    /**
     * Sets sender name
     *
     * @param string $fromName Sender name
     * @return self
     */
    public function setFromName(string $fromName): self
    {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * Sets path to PHPMailer class file
     *
     * @param string $phpMailerPath Path to PHPMailer
     * @return self
     */
    public function setPhpMailerPath(string $phpMailerPath): self
    {
        $this->phpMailerPath = $phpMailerPath;
        return $this;
    }
}
