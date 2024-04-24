<?php

namespace frontend;

use Authenticated;
use authentication\AuthenticationCookie;
use authentication\StormUser;
use Controller;
use frontend\account\ProfileService;
use frontend\account\ProfileStore;
use frontend\comments\CommentFinder;
use infrastructure\PageCriteria;
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
        private ProfileService $accountService,
        private ProfileStore   $profileStore,
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

    #[Route("/profile")]
    #[Authenticated]
    public function profile(): View
    {
        $profile = $this->profileStore->loadProfile($this->user->id);

        $profilePhotoUpdated = null;
        if ($this->request->isPost()) {
            $photo = $this->request->getFile('profile-photo');
            if ($photo->wasUploaded()) {
                $profilePhotoUpdated = $this->accountService->updateProfilePhoto($photo);
            }
        }

        $view = view('@frontend/account');
        $view->profileUpdated = $profilePhotoUpdated;
        $view->profile = $profile;
        $view->maxFileSize = $this->settings->upload->maxFileSize;
        $view->maxPhotoSize = $this->settings->upload->maxPhotoSize;

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
