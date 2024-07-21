<?php

namespace app\shared\presentation;

class ReplyDto
{
    public int $id;
    public string $content;
    public string $created_at;
    public UserProfileDto $author;
}