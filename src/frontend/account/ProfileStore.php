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

    public function updateAboutMe(int $id, string $aboutMe): void
    {
        $query = "UPDATE users SET about_me = ? WHERE id = ?";
        $this->database->update($query, $aboutMe, $id);
    }

    public function loadProfile(int $id): object
    {
        $query = "SELECT photo, name, about_me FROM users AS u WHERE u.id = ?";
        return $this->database->fetchOne($query, $id);
    }
}