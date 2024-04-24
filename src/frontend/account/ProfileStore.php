<?php

namespace frontend\account;

use infrastructure\Database;

readonly class ProfileStore
{
    function __construct (
        private Database $database
    ) { }

    public function updateProfilePhoto(int $id, string $profile): void
    {
        $query = "UPDATE users SET photo = ? WHERE id = ?";
        $this->database->update($query, $profile, $id);
    }

    public function loadProfile(int $id): object
    {
        $query = "SELECT photo, name FROM users AS u WHERE u.id = ?";
        return $this->database->fetchOne($query, $id);
    }
}