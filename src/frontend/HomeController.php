<?php

use authentication\StormUser;
use frontend\ArticlesFinder;
use frontend\CommentFinder;
use infrastructure\settings\Settings;
use infrastructure\Slug;

#[Controller]
readonly class Homepage
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
        $language = $this->user->language->primary;
        $articles = $this->articlesFinder->find($language);
        $data = ['articles' => $articles];

        return view("@frontend/home", $data);
    }

    #[Route("/:slug")]
    public function article(): View
    {
        $slug = $this->request->getParameter('slug');
        list($id) = $this->slug->getParameters($slug);

        $comments = $this->commentFinder->find($id);
        $article = $this->articlesFinder->findOne($id);

        return view('@frontend/article', [
            'slug' => $slug,
            'article' => $article,
            'comments' => $comments,
            'settings' => $this->settings]);
    }
}
