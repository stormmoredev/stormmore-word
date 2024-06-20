<?php

namespace app\authentication;

use Cookies;
use stdClass;

readonly class AuthenticationCookie
{
    public function __construct(
        private UserSecret $userSecret
    ) { }

    public function addUser($user, string $sessionKey): void
    {
        $jsonUser = new stdClass();
        $jsonUser->name = $user->name;
        $jsonUser->panel = $user->role != StormUser::$READER;
        $jsonUser->photo = $user->photo;
        $jsonUser->key = $this->userSecret->encrypt($sessionKey);
        Cookies::set('storm-user', json_encode($jsonUser));
    }

    public function update($field, $photo): void
    {
        $user = Cookies::get('storm-user');
        $user = json_decode($user);
        $user->$field = $photo;
        Cookies::set('storm-user', json_encode($user));
    }

    public function has(): bool
    {
        return Cookies::has('storm-user');
    }

    public function get(): ?string
    {
        $user = Cookies::get('storm-user');
        $user = json_decode($user);
        return $this->userSecret->decrypt($user->key);
    }

    public function delete(): void
    {
        Cookies::delete('storm-user');
    }
}