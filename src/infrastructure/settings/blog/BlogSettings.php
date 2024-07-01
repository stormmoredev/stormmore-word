<?php

namespace infrastructure\settings\blog;

readonly class BlogSettings
{
    public bool $enabled;

    public function __construct(
        public CommentsSettings $comments = new CommentsSettings()
    ) { }
}