<?php

namespace authentication;

use Cookies;
use stdClass;

readonly class AuthenticationCookie
{
    public function __construct(
        private UserSecret $userSecret
    ) { }

    public function addUser($user): void
    {
        $jsonUser = new stdClass();
        $jsonUser->name = $user->name;
        Cookies::set('user', json_encode($jsonUser));
    }

    public  function addSessionKey($sessionKey): void
    {
        $cipher = $this->userSecret->encrypt($sessionKey);

        Cookies::set('storm', $cipher);
    }

    public function has(): bool
    {
        return Cookies::has('storm');
    }

    public function get(): ?string
    {
        $encrypted = Cookies::get('storm');
        return $this->userSecret->decrypt($encrypted);
    }

    public function delete(): void
    {
        Cookies::delete('storm');
        Cookies::delete('user');
    }
}