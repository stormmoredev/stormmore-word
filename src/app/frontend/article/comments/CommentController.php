<?php

namespace app\frontend\article\comments;

use app\authentication\StormUser;
use Authenticate;
use Controller;
use Redirect;
use Request;
use Response;
use ResponseCache;
use Route;

#[Controller]
#[Authenticate]
readonly class CommentController
{
    public function __construct(
        private StormUser      $user,
        private Request        $request,
        private Response       $response,
        private ResponseCache  $responseCache,
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

        $this->responseCache->delete("$articleId-*");

        //$this->response->setFlashFlag('comment-success');
        return redirect("/$slug#comment-$commentId");
    }
}