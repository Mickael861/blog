<?php
namespace App\Utils;

use App\Exception\MailerException;
use PHPMailer\PHPMailer\PHPMailer as PHPMailerPHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class PhpMailer extends PHPMailerPHPMailer
{

    /**
     * @var string
     */
    private $fromMail;

    /**
     * @var string
     */
    private $toMail;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $host = 'localhost';

    /**
     * @var int
     */
    private $port = 1025;

    /**
     * @var string
     */
    private $charset = 'UTF-8';

    public function __construct(bool $exception = false)
    {
        parent::__construct($exception);
    }

    /**
     * Set data to email
     *
     * @param  array $datas_mail datas of the mail
     * @return bool true, if the sending of the contact email was successful, false otherwise
     */
    public function addDatasMail(array $datas_mail)
    {
        foreach ($datas_mail as $key => $data) {
            $seter = 'set' . $key;
            if (method_exists(__CLASS__, $seter)) {
                $this->{$seter}($data);

                continue;
            }

            throw new MailerException('La fonction ' . __CLASS__ . '\\' . $seter . ' n\'existe pas');
        }
        
        return $this->sendMail();
    }
    
    /**
     * sendMail
     *
     * @return void
     */
    private function sendMail(): bool
    {
        try {
            $this->isSMTP();
            $this->Host = $this->getHost();
            $this->Port = $this->getPort();

            $this->Charset = $this->getCharset();

            $this->setFrom($this->getFromMail());

            $this->addAddress($this->getToMail());

            $this->Subject = '=?UTF-8?B?' . base64_encode($this->getSubject()) . '?=';
            $this->Body = wordwrap($this->getBody());

            $this->send();
        } catch (PHPMailerException $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the value of from
     */
    private function getFromMail(): string
    {
        return $this->fromMail;
    }

    /**
     * Set the value of from
     *
     * @param string $fromMail
     * @return self
     */
    private function setFromMail(string $fromMail): self
    {
        $this->fromMail = $fromMail;

        return $this;
    }

    /**
     * Get the value of to
     */
    private function getToMail(): string
    {
        return $this->toMail;
    }

    /**
     * Set the value of to
     *
     * @param string $toMail
     * @return self
     */
    private function setToMail(string $toMail): self
    {
        $this->toMail = $toMail;

        return $this;
    }

    /**
     * Get the value of subject
     */
    private function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @param string $toMail
     * @return self
     */
    private function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of body
     */
    private function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @param string $body
     * @return self
     */
    private function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of host
     */
    private function getHost(): string
    {
        return $this->host;
    }

    /**
     * Set the value of host
     *
     * @param string $host
     * @return self
     */
    private function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the value of port
     */
    private function getPort(): int
    {
        return $this->port;
    }

    /**
     * Set the value of port
     *
     * @param int $port
     * @return self
     */
    private function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the value of charset
     */
    private function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Set the value of charset
     *
     * @param string $charset
     * @return self
     */
    private function setCharset(string $charset): self
    {
        $this->charset = $charset;

        return $this;
    }
}
