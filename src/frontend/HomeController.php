<?php

namespace frontend;

use Authenticated;
use authentication\StormUser;
use Controller;
use frontend\account\AccountService;
use frontend\comments\CommentFinder;
use infrastructure\settings\Settings;
use infrastructure\Slug;
use Request;
use Route;
use View;

#[Controller]
readonly class HomeController
{
    public function __construct(
        private StormUser      $user,
        private Request        $request,
        private Slug           $slug,
        private Settings       $settings,
        private CommentFinder  $commentFinder,
        private AccountService $accountService,
        private ArticlesFinder $articlesFinder)
    {
    }

    #[Route("/")]
    public function index(): View
    {
        $lang = $this->user->language->primary;
        $article = $this->articlesFinder->find($lang);

        $view = view("@frontend/home");
        $view->articles = $article;

        return $view;
    }

    #[Route("/account")]
    #[Authenticated]
    public function account(): View
    {
        $view = view('@frontend/account');
        $view->profileUpdated = null;

        if ($this->request->isPost()) {
            $view->profileUpdated = $this->accountService->updateProfilePhoto();
        }

        return $view;
    }

    #[Route("/:slug")]
    public function article(): View
    {
        $slug = $this->request->getParameter('slug');
        list($id) = $this->slug->getParameters($slug);

        $comments = $this->commentFinder->find($id);
        $article = $this->articlesFinder->findOne($id);

        $view = view('@frontend/article');
        $view->slug = $slug;
        $view->article = $article;
        $view->comments = $comments;
        $view->settings = $this->settings;

        return $view;
    }
}
