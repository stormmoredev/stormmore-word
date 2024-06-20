<?php

namespace infrastructure;

use app\authentication\StormUser;
use infrastructure\settings\Settings;

readonly class MailNotifications
{
    public function __construct(
        private Settings $settings,
        private StormMailer $mailer,
        private StormUser $user
    ) { }

    public function SmtpTest(): void
    {
        $mail = new StormMail();
        $mail->setSubject(_("SMTP server mail test"))
            ->setBody(_("Lorem ipsum"))
            ->setRecipient($this->user->email);

        $this->mailer->send($mail);
    }

    public function signupConfirmation(string $recipient, string $token): void
    {
        $url = $this->settings->url;
        $url .= str_ends_with($url, "/") ? "" : "/";
        $href = url($url . 'confirm-email', ["token" => $token]);
        $content = _('Here will be activation link: <a href="%s">link</a>', $href);
        $content = $this->buildBody($content);

        $mail = new StormMail();
        $mail->setSubject(_("Thank you for signing up!"))
            ->setBody($content)
            ->setRecipient($recipient);

        $this->mailer->send($mail);
    }

    private function buildBody($content): string
    {
        $path = resolve_path_alias('@frontend/mail.php');
        file_exists($path) or throw new Exception("MailNotifications: template [$path] doesn't exist");
        $template = file_get_contents($path);
        return str_replace('%content%', $content, $template);
    }
}