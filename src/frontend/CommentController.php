<?php

namespace frontend;

use authentication\StormUser;
use Request;
use Response;
use Authenticated;
use Controller;
use Route;
use Redirect;

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