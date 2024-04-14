<?php

namespace authentication;

use IdentityUser;
use Language;

class StormUser extends IdentityUser
{
    const string READER = "reader";
    const string ADMINISTRATOR = 'administrator';

    public string $role;

    public Language $language;

    public function canEnterPanel(): bool
    {
        return $this->isAuthenticated() and !empty($this->role) and $this->role != 'reader';
    }
}