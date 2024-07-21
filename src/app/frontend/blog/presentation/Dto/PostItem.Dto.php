<?php

namespace app\frontend\blog\presentation\Dto;

use app\shared\presentation\UserProfileDto;

class PostItemDto
{
    public string $slug;
    public string $title;
    public string $subtitle;
    public ?string $titled_media;
    public int $votes_num;
    public int $replies_num;
    public string $published_at;

    public UserProfileDto $author;
}