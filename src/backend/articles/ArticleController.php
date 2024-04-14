<?php

namespace backend;

use Controller, Route, Request, Response, View;

import('@/backend/articles/*');

#[Controller]
#[Route("/admin/articles")]
readonly class ArticleController
{
    public function __construct(
        private Request $request,
        private Response $response,
        private ArticleStore $articleStore,
        private ArticleFinder $articleFinder,
        private ArticleService $articleService
    ) { }

    #[Route("/admin", "/admin/articles")]
    function index(): View  {
        $articles = $this->articleFinder->find();
        return view('@backend/articles/index', ['articles' => $articles]);
    }

    function edit(): View {
        $article = new ArticleDto();
        if ($this->request->exist('article-id')) {
            $article->id = $this->request->parameters['article-id'];
            $article = $this->articleStore->find($article->id);
        }
        return view('@backend/articles/edition', $article);
    }

    function save() {
        $article = $this->request->body;
        return $this->articleService->save($article);
    }

    function publish(): void {
        $articleId = $this->request->parameters['article-id'];
        $this->articleService->setPublishStatus($articleId, true);
    }

    function unPublish(): void {
        $articleId = $this->request->parameters['article-id'];
        $this->articleService->setPublishStatus($articleId, false);
    }
}
