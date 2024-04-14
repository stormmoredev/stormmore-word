<?php

namespace infrastructure\settings;

class MailSettings
{
    public string $host;
    public bool $isAuthenticationEnabled;
    public string $username;
    public string $password;
    public bool $isTlsEnabled;
    public ?int $port;

    public function __construct(
        public From $from = new From()
    ) { }
}