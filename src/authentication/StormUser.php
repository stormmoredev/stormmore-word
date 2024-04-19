<?php

namespace authentication;

use IdentityUser;
use Language;
use Override;

class StormUser extends IdentityUser
{
    const string READER = "reader";
    const string ADMINISTRATOR = 'administrator';

    public string $role = "reader";

    public Language $language;

    public function canEnterPanel(): bool
    {
        return $this->isAuthenticated() and !empty($this->role) and $this->role != 'reader';
    }

    #[Override]
    public function hasClaims(array $claims): bool
    {
        if ($this->role == self::ADMINISTRATOR)
        {
            return true;
        }
        return parent::hasClaims($claims);
    }
}