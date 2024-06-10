<?php

namespace authentication;

use IdentityUser;
use Language;
use Override;

class StormUser extends IdentityUser
{
    public static string $READER = "reader";
    public static string $ADMINISTRATOR = 'administrator';

    public string $role = "reader";

    public Language $language;

    public ?string $photo = null;

    public function canEnterPanel(): bool
    {
        return $this->isAuthenticated() and !empty($this->role) and $this->role != 'reader';
    }

    public function hasPhoto(): bool
    {
        return $this->photo != null;
    }

    #[Override]
    public function hasClaims(array $claims): bool
    {
        if ($this->role == self::$ADMINISTRATOR)
        {
            return true;
        }
        return parent::hasClaims($claims);
    }
}