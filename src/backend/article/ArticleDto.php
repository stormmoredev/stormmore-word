<?php

namespace backend;

use DateTime;

class ArticleDto
{
    public int $id = 0;
    public string $title = "";
    public string $content = "";
    public string $author_name;
    public DateTime $created_at;
    public DateTime $updated_at;
    public DateTime $published_at;
}