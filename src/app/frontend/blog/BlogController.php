<?php

/** @noinspection PhpUnused */

namespace app\frontend\blog;

use AjaxAuthenticate;
use app\frontend\blog\domain\BlogService;
use app\frontend\blog\presentation\PostFinder;
use app\shared\presentation\ReplyFinder;
use Authenticate;
use Controller;
use I18n;
use infrastructure\AjaxResult;
use infrastructure\ModuleRouter;
use infrastructure\settings\Settings;
use PostMethod;
use Redirect;
use Request;
use ResponseCache;
use Route;
use View;

#[Controller]
readonly class BlogController
{
    public function __construct(
        private ResponseCache $responseCache,
        private Request       $request,
        private Settings      $settings,
        private I18n          $i18n,
        private PostFinder    $postFinder,
        private ReplyFinder   $replyFinder,
        private ModuleRouter  $homeRouter,
        private BlogService   $blogService)
    {
    }

    #[Route("/b")]
    public function index(): View
    {
        return $this->homeRouter->blog();
    }

    public function js(): View
    {
        return view('@frontend/js');
    }

    #[Route("/b/:slug")]
    public function post(): View
    {
        $this->responseCache->cache();

        $slug = $this->request->getParameter('slug');
        $post = $this->postFinder->getBySlug($slug);
        $replies = $this->replyFinder->find($slug);

        return view('@frontend/blog/post', [
            'post' => $post,
            'slug' => $slug,
            'replies' => $replies,
            'settings' => $this->settings,
            'js_format' => $this->i18n->culture->dateTimeFormat
        ]);
    }

    #[AjaxAuthenticate]
    #[PostMethod]
    #[Route("/b/vote/:id")]
    public function votePost(): AjaxResult
    {
        $id = $this->request->getParameter('id');
        return $this->blogService->vote($id);
    }

    #[AjaxAuthenticate]
    #[PostMethod]
    #[Route("/b/rmvote/:id")]
    public function unvotePost(): AjaxResult
    {
        $id = $this->request->getParameter('id');
        return $this->blogService->removeVote($id);
    }

    #[Authenticate]
    #[Route("/b/add-reply/:slug")]
    public function addReply(): Redirect
    {
        list($postId, $content, $slug) = $this->request->get('post-id', 'content', 'slug');
        $commentId = $this->blogService->addComment($postId, $content);

        $this->responseCache->delete("$postId-*");

        return redirect("/b/$slug#comment-$commentId", 'comment-success');
    }

    #[Authenticate]
    #[Route("/b/add-post")]
    public function addPost(): View|Redirect
    {
        $form = new AddPostForm($this->request);

        if ($form->isSubmittedSuccessfully()) {
            $post = $this->request->toObject(['title', 'subtitle', 'content', 'media']);
            $slug = $this->blogService->addPost($post);
            return redirect("/b/$slug");
        }
        return view('@frontend/blog/add-post', [
            'form' => $form
        ]);
    }
}