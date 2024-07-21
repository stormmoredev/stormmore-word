<?php

namespace app\shared\presentation;

class UserProfileDto
{
    public string $name;
    public string $slug;
    public ?string $photo;
    public int $entries_num;
    public int $followers_num;
}