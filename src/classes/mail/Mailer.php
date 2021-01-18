<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once(LIB_DIR . DIR_SEP . 'PHPMailer' . DIR_SEP . 'src' . DIR_SEP . 'PHPMailer.php');

class Mailer
{
    const MAIL_TEMPLATE_DIR = PUBLIC_DIR . DIR_SEP . 'mail' . DIR_SEP . 'template';

    private $mailer;

    public function __construct(MailAuthConfig $auth, MailHeaderConfig $header)
    {
        $this->mailer = new PHPMailer(true);
        $this->setUpAuth($auth);
        $this->setUpHeader($header);
    }

    private function setUpAuth(MailAuthConfig $auth)
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $auth->getHost();
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPSecure = $auth->getSMTPSecure();
        $this->mailer->Username = $auth->getUsername();
        $this->mailer->Password = $auth->getPassword();
        $this->mailer->Port = $auth->getPort();
    }

    private function setUpHeader(MailHeaderConfig $header)
    {
        $this->mailer->From = $header->getFrom();
        $this->mailer->addAddress($header->getTo());
        $this->mailer->Sender = $header->getFrom();
        $this->mailer->CharSet = $header->getCharset();
        $this->mailer->Subject = $header->getSubject();
    }

    public static function getMailTemplate(string $templateName)
    {
        echo self::MAIL_TEMPLATE_DIR . DIR_SEP . $templateName;
        $template = include(self::MAIL_TEMPLATE_DIR . DIR_SEP . $templateName);
        return ($template === false) ? null : $template;
    }

    public function setBody(string $body)
    {
        $this->mailer->Body = $body;
    }

    public function send()
    {
        $this->mailer->send();
    }

    public function setDebug(int $level)
    {
        $this->mailer->SMTPDebug = $level;
    }
}
