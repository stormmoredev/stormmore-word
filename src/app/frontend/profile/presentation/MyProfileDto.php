<?php

namespace app\frontend\profile\presentation;

use app\shared\presentation\UserProfileDto;

class MyProfileDto extends UserProfileDto
{
    public ?string $about_me;

    public int $entries_num;
}