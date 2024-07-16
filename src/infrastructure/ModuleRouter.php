<?php

namespace infrastructure;

use app\authentication\StormUser;
use app\frontend\blog\PostFinder;
use app\frontend\Exception;
use app\frontend\forum\ForumFinder;
use infrastructure\settings\Settings;
use Request;
use View;

readonly class ModuleRouter
{
    public function __construct (
        private Settings    $settings,
        private StormUser   $user,
        private Request     $request,
        private PostFinder  $postFinder,
        private ForumFinder $forumFinder
    ) { }

    public function homepage(): View
    {
        $homepage = $this->settings->homepage;
        return match ($homepage) {
            'b' => $this->blog(),
            'c' => $this->community(),
            'f' => $this->forum()
        };
    }

    public function forum(): View
    {
        $this->settings->forum->enabled or throw new Exception("", 404);
        $threads = $this->forumFinder->listThreads();
        return view('@frontend/forum/index', [
            'threads' => $threads,
            'category' => null
        ]);
    }

    public function blog(): View
    {
        $lang = $this->user->language->primary;
        $posts = $this->postFinder->findPosts($lang);

        return view("@frontend/blog/list", [
            'settings'  =>  $this->settings,
            'posts'     =>  $posts,
            'count' => 55
        ]);
    }
    
    public function community(): View
    {
        return view("@frontend/community/index");
    }
}