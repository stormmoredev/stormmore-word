<?php

namespace app\frontend\profile\domain;

use infrastructure\Database;

readonly class ProfileRepository
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
}