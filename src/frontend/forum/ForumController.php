<?php

namespace frontend\forum;

use Controller, Route, View, Redirect, Request, Authenticate;
use infrastructure\settings\Settings;

#[Controller]
readonly class ForumController
{
    public function __construct(
        private Request      $request,
        private Settings     $settings,
        private ForumFinder  $forumFinder,
        private ForumService $forumService)
    { }

    #[Route("/f")]
    public function forum(): View
    {
        if (!$this->settings->forum->enabled) {
            throw new Exception("", 404);
        }
        $threads = $this->forumFinder->listThreads();
        return view('@frontend/forum/threads-list', ['threads' => $threads]);
    }

    #[Authenticate]
    #[Route("/f/add-thread")]
    public function addThread(): View|Redirect
    {
        if ($this->request->isPost()) {
            $title = $this->request->getParameter('title');
            $content = $this->request->getParameter('content');
            $id = $this->forumService->addThread($title, $content);
            return redirect("/f/thread/$id");
        }
        return view('@frontend/forum/add-thread');
    }

    #[Route('/f/thread/:id')]
    public function thread(): View
    {
        $id = $this->request->getParameter('id');
        $thread = $this->forumFinder->getById($id);
        $replies = $this->forumFinder->listReplies($id);
        return view('@frontend/forum/thread', ['thread' => $thread, 'replies' => $replies]);
    }

    #[Route('/f/add-reply')]
    public function addReply(): Redirect
    {
        $threadId = $this->request->getParameter('thread-id');
        $content = $this->request->getParameter('content');
        $this->forumService->addPost($threadId, $content);
        return redirect('/f/thread/' . $threadId);
    }
}