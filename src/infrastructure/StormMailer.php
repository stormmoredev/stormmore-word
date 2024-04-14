<?php

namespace infrastructure;

use infrastructure\settings\Settings;
use PHPMailer\PHPMailer\PHPMailer;

import ('@vendor/phpmailer/src/Exception');
import ('@vendor/phpmailer/src/PHPMailer');
import ('@vendor/phpmailer/src/SMTP');

readonly class StormMailer
{
    public function __construct(
        private Settings $settings
    ) { }

    public function send(StormMail $mail): void
    {
        $fromName = $this->settings->mail->from->name;
        $fromAddress = $this->settings->mail->from->address;

        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->isHTML();
        $mailer->Timeout = 2;
        $mailer->Host       = $this->settings->mail->host;
        $mailer->SMTPAuth   = $this->settings->mail->isAuthenticationEnabled;
        $mailer->Username   = $this->settings->mail->username;
        $mailer->Password   = $this->settings->mail->password;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        if ($this->settings->mail->isTlsEnabled) {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mailer->Port       = $this->settings->mail->port;

        $mailer->setFrom($fromAddress, $fromName);
        $mailer->Subject = $mail->subject;
        $mailer->Body = $mail->body;
        $mailer->addAddress($mail->recipient);

        $mailer->send();
    }
}