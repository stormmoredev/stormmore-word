<?php

namespace entries\forum;

use Controller;
use Route;

#[Controller]
class ForumController
{
    public function __construct (
    ) { }

    #[Route("/admin/forum/threads", "/admin/forum")]
    public function threads()
    {
        return view('@backend/forum/threads');
    }

    #[Route("/admin/forum/categories")]
    public function categories()
    {
        return view('@backend/forum/categories');
    }
}