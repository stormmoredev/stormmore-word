<?php

namespace entries\forum;

use backend\forum\ThreadFinder;
use Controller;
use Route;
use View;

#[Controller]
readonly class ForumController
{
    public function __construct (
        private ThreadFinder $threadFinder
    ) { }

    #[Route("/admin/forum/threads", "/admin/forum")]
    public function threads(): View
    {
        $threads = $this->threadFinder->find();
        return view('@backend/forum/threads', ['threads' => $threads]);
    }

    #[Route("/admin/forum/categories")]
    public function categories(): View
    {
        return view('@backend/forum/categories');
    }
}