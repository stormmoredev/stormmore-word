<?php

namespace app\frontend\blog\presentation\Dto;

use app\shared\presentation\UserProfileDto;

class PostDto
{
    public int $id;
    public string $title;
    public string $subtitle;
    public string $content;
    public int $votes_num;
    public int $replies_num;
    public string $published_at;

    public UserProfileDto $author;
}