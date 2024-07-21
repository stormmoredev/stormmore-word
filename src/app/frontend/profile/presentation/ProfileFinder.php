<?php

namespace app\frontend\profile\presentation;

use infrastructure\Database;
use Mapper;

readonly class ProfileFinder
{
    function __construct (
        private Database $database
    ) { }

    public function findMyProfile(int $id): MyProfileDto
    {
        $query = "SELECT name, slug, photo, about_me FROM users AS u WHERE u.id = ?";
        $result = $this->database->fetchOne($query, $id);
        $myProfile = new MyProfileDto();
        Mapper::map($result, $myProfile, ['name', 'photo', 'slug', 'about_me']);
        return $myProfile;
    }
}