<?php

use app\authentication\StormUser;
use app\frontend\blog\BlogService;
use app\frontend\blog\PostFinder;
use infrastructure\AjaxResult;
use infrastructure\ModuleRouter;
use infrastructure\settings\Settings;
use infrastructure\Slug;
use Form;

#[Controller]
readonly class BlogController
{
    public function __construct(
        private StormUser      $user,
        private ResponseCache  $responseCache,
        private Request        $request,
        private Slug           $slug,
        private Settings       $settings,
        private I18n           $i18n,
        private PostFinder     $postFinder,
        private ModuleRouter   $homeRouter,
        private BlogService    $blogService)
    {
    }

    #[Route("/b")]
    public function index(): View
    {
        return $this->homeRouter->blog();
    }

    #[Route("/b/:slug")]
    public function post(): View
    {
        $this->responseCache->cache();

        $slug = $this->request->getParameter('slug');
        $comments = $this->postFinder->findComments($slug);
        $post = $this->postFinder->getBySlug($slug);

        return view('@frontend/blog/post', [
            'slug' => $slug,
            'post' => $post,
            'comments' => $comments,
            'settings' => $this->settings,
            'js_format' => $this->i18n->culture->dateTimeFormat
        ]);
    }

    #[AjaxAuthenticate]
    #[PostMethod]
    #[Route("/b/vote/:slug")]
    public function votePost(): AjaxResult
    {
        $slug = $this->request->getParameter('slug');
        return $this->blogService->vote($slug);
    }

    #[Authenticate]
    #[Route("/b/add-comment/:slug")]
    public function addComment(): Redirect
    {
        list($slug, $content, $articleId) = $this->request->get('slug', 'content', 'article-id');
        $commentId = $this->blogService->addComment($articleId, $content);

        $this->responseCache->delete("$articleId-*");

        return redirect("/b/$slug#comment-$commentId");
    }

    #[Authenticate]
    #[Route("/b/add-post")]
    public function addPost(): View|Redirect
    {
        $form = new Form($this->request);
        $form->rules = [
            'title' => ['required', 'maxlen' => 128],
            'subtitle' => ['required', 'maxlen' => 256],
            'content' => ['required', 'maxlen' => 256],
            'media' => ['titled_media']
        ];
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