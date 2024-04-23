<?php

namespace frontend\account;

use infrastructure\Database;

readonly class AccountStore
{
    function __construct (
        private Database $database
    ) { }

    public function updateProfile(int $id, string $profile): void
    {
        $query = "UPDATE users SET photo = ? WHERE id = ?";
        $this->database->update($query, $profile, $id);
    }
}