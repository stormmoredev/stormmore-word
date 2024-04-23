<?php

namespace frontend\comments;

use Authenticated;
use authentication\StormUser;
use Controller;
use Redirect;
use Request;
use Response;
use Route;

#[Controller]
#[Authenticated]
readonly class CommentController
{
    public function __construct(
        private StormUser      $user,
        private Request        $request,
        private Response       $response,
        private CommentService $commentService)
    {
    }

    #[Route("/add-comment/:slug")]
    public function addComment(): Redirect
    {
        $userId = $this->user->id;
        $slug = $this->request->getParameter('slug');
        $content = $this->request->getParameter('content');
        $articleId = $this->request->getParameter('article-id');
        $commentId = $this->commentService->add($userId, $articleId, $content);

        $this->response->setFlashFlag('comment-success');
        return redirect("/$slug#comment-$commentId");
    }
}