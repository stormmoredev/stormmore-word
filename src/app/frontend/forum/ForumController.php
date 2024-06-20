<?php

namespace app\frontend\forum;

use Authenticate;
use Controller;
use frontend\forum\Exception;
use infrastructure\settings\Settings;
use Redirect;
use Request;
use Route;
use View;
use Form;

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
    public function index(): View
    {
        if (!$this->settings->forum->enabled) {
            throw new Exception("", 404);
        }
        $threads = $this->forumFinder->listThreads(null);
        $categories = $this->forumFinder->listCategories();
        $data = ['threads' => $threads, 'categories' => $categories];
        return view('@frontend/forum/index', $data);
    }

    #[Route("/f/c/:cid")]
    public function category(): View
    {
        $cid = $this->request->get('cid');
        $categories = $this->forumFinder->listCategories();
        $category = $this->forumFinder->getCategoryById($cid);
        $threads = $this->forumFinder->listThreads($cid);
        $data = ['category' => $category, 'threads' => $threads, 'categories' => $categories];
        return view('@frontend/forum/category', $data);
    }

    #[Authenticate]
    #[Route("/f/add-thread")]
    public function addThread(): View|Redirect
    {
        $cid = $this->request->getInt('c');
        $form = new Form($this->request);
        $form->rules = [
            'title' => ['required'],
            'content' => ['required'],
            'c' => ['required']
        ];

        if ($form->isSubmittedSuccessfully()) {
            list($title, $content) = $this->request->get('title', 'content');
            $id = $this->forumService->addThread($cid, $title, $content);
            return redirect("/f/thread/$id");
        }

        $view = view('@frontend/forum/add-thread');
        $view->selectCategories = !$this->request->hasGetParameter('c');
        $view->cid = $cid;
        $view->form = $form;
        if ($view->selectCategories) {
            $view->categories = $this->forumFinder->listCategories();
        } else {
            $view->category = $this->forumFinder->getCategoryById($cid);
        }
        return $view;
    }

    #[Route('/f/thread/:id')]
    public function thread(): View
    {
        $id = $this->request->getParameter('id');
        $thread = $this->forumFinder->getThreadById($id);
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