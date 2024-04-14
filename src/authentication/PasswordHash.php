<?php

namespace authentication;

class PasswordHash
{
    public static function hash(string $password): string
    {
        return hash('sha256', $password);
    }
}