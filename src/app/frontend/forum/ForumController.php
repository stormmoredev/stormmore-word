<?php

namespace app\frontend\forum;

use Authenticate;
use Controller;
use Form;
use Exception;
use infrastructure\CategoriesTree;
use infrastructure\routing\Routing;
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
        private Routing      $routing)
    { }

    #[Route("/f", "/f/:route")]
    public function index(): View
    {
        $this->settings->forum->enabled or throw new Exception("", 404);

        $slug = null;
        if ($this->request->has('route')) {
            $route = $this->request->get('route');
            $route = $this->routing->parse($route);
            if ($route->isEntry()) {
                return $this->thread($route->id);
            }
            $slug = $route->slug;
        }

        return $this->threads($slug);
    }

    private function threads(?string $slug): View
    {
        $category = null;
        if ($slug !== null) {
            $category = $this->forumFinder->getCategoryBySlug($slug);
            $category !== null or throw new Exception("Forum $slug not found.", 404);
        }
        $threads = $this->forumFinder->listThreads($slug);
        return view('@frontend/forum/index', [
            'threads' => $threads,
            'category' => $category,
            'routing' => $this->routing
        ]);
    }

    private function thread($id): View
    {
        $thread = $this->forumFinder->getThreadById($id);
        $thread !== null or throw new Exception("Thread not found.", 404);
        $thread = $this->forumFinder->getThreadById($id);
        $replies = $this->forumFinder->listReplies($id);
        return view('@frontend/forum/thread', [
            'thread' => $thread,
            'replies' => $replies
        ]);
    }

    private function list(string $slug): View
    {
        $category = $this->forumFinder->getCategoryBySlug($slug);
        $threads = $this->forumFinder->listThreads($slug);
        return view('@frontend/forum/index', [
            'threads' => $threads,
            'category' => $category,
            'cid' => $category->id,
            'routing' => $this->routing
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
            $id = $this->forumService->addThread($cid, $title, $content);
            return redirect($this->routing->forumThreadByTitleAndId($title, $id));
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
        return redirect('/f/thread/' . $threadId);
    }
}