<?php

namespace infrastructure\settings;

class AuthenticationSettings
{
    public bool $enabled;

    public function isAuthByProvidersEnabled(): bool
    {
        return $this->facebook->enabled or $this->google->enabled or $this->wordpress->enabled;
    }

    public function __construct (
        public AuthenticationProvider $facebook = new AuthenticationProvider(),
        public AuthenticationProvider $google = new AuthenticationProvider(),
        public AuthenticationProvider $wordpress = new AuthenticationProvider()
    ) { }
}