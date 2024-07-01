<?php

namespace app\frontend\forum;

use Authenticate;
use Controller;
use Exception;
use Form;
use infrastructure\CategoriesTree;
use infrastructure\ModuleRouter;
use infrastructure\settings\Settings;
use Redirect;
use Request;
use Route;
use View;

#[Controller]
readonly class ForumController
{
    public function __construct(
        private Request      $request,
        private Settings     $settings,
        private ForumFinder  $forumFinder,
        private ForumService $forumService,
        private ModuleRouter $homeRouter)
    { }

    #[Route("/f")]
    public function homepage(): View
    {
        return $this->homeRouter->forum();
    }

    #[Route("/f/:slug")]
    public function thread(): View
    {
        $slug = $this->request->get("slug");
        $thread = $this->forumFinder->getThreadBySlug($slug);
        $thread !== null or throw new Exception("Thread not found.", 404);

        $replies = $this->forumFinder->listReplies($slug);
        return view('@frontend/forum/thread', [
            'thread' => $thread,
            'replies' => $replies
        ]);
    }

    #[Route("/fc/:slug")]
    public function category(): View
    {
        $this->settings->forum->enabled or throw new Exception("", 404);

        $slug = $this->request->get('slug');
        $threads = $this->forumFinder->listThreads($slug);
        $category = $this->forumFinder->getCategoryBySlug($slug);
        return view('@frontend/forum/index', [
            'threads' => $threads,
            'category' => $category
        ]);
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
            $slug = $this->forumService->addThread($cid, $title, $content);
            return redirect("/f/$slug");
        }

        $hasUrlCategoryParameter = $this->request->hasGetParameter('c');
        $view = view('@frontend/forum/add-thread');
        $view->selectCategories = !$hasUrlCategoryParameter;
        $view->cid = $cid;
        $view->form = $form;
        $view->category = null;
        if ($view->selectCategories) {
            $tree = new CategoriesTree($this->forumFinder->listCategories());
            $view->categories = $tree->toFlat();
        } else {
            $view->category = $this->forumFinder->getCategoryById($cid);
        }
        return $view;
    }

    #[Route('/f/add-reply')]
    public function addReply(): Redirect
    {
        $threadId = $this->request->getParameter('thread-id');
        $content = $this->request->getParameter('content');
        $this->forumService->addPost($threadId, $content);
        $thread = $this->forumFinder->getThreadById($threadId);
        return redirect("/f/$thread->slug");
    }
}