<?php

namespace app\backend\article;

use Controller;
use Request;
use ResponseCache;
use Route;
use View;

import('@/backend/articles/*');

#[Controller]
#[Route("/admin/articles")]
readonly class ArticleController
{
    public function __construct(
        private Request        $request,
        private ArticleStorage $articleStore,
        private ArticleFinder  $articleFinder,
        private ArticleService $articleService,
        private ResponseCache  $responseCache
    ) { }

    #[Route("/admin", "/admin/articles")]
    function index(): View  {
        $articles = $this->articleFinder->find();
        return view('@backend/articles/index', ['articles' => $articles]);
    }

    function edit(): View {
        $article = new ArticleDto();
        if ($this->request->hasParameter('article-id')) {
            $article->id = $this->request->parameters['article-id'];
            $article = $this->articleStore->find($article->id);
        }
        return view('@backend/articles/edition', $article);
    }

    function save() {
        $article = $this->request->body;
        $this->articleService->save($article);
        $this->responseCache->delete("$article->id-*");
        return $article->id;
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
