<?php

namespace frontend;

use Controller, Route, Request, View;
use authentication\StormUser;
use infrastructure\settings\Settings;
use infrastructure\Slug;

#[Controller]
readonly class HomeController
{
    public function __construct(
        private StormUser      $user,
        private Request        $request,
        private Slug           $slug,
        private Settings       $settings,
        private CommentFinder $commentFinder,
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
