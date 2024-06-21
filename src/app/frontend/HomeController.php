<?php

namespace app\frontend;

use app\authentication\StormUser;
use app\frontend\account\ProfileService;
use app\frontend\account\ProfileStorage;
use app\frontend\article\ArticleFinder;
use app\frontend\article\comments\CommentFinder;
use Authenticate;
use Controller;
use I18n;
use infrastructure\settings\Settings;
use infrastructure\Slug;
use Request;
use Response;
use ResponseCache;
use Route;
use View;

#[Controller]
readonly class HomeController
{
    public function __construct(
        private StormUser      $user,
        private Response       $response,
        private ResponseCache  $responseCache,
        private Request        $request,
        private Slug           $slug,
        private Settings       $settings,
        private CommentFinder  $commentFinder,
        private ProfileService $accountService,
        private ProfileStorage $profileStore,
        private I18n           $i18n,
        private ArticleFinder  $articlesFinder)
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
    #[Authenticate]
    public function profile(): View
    {
        $profilePhotoUpdated = null;
        if ($this->request->isPost()) {
            $photo = $this->request->getFile('profile-photo');
            if ($photo?->isUploaded()) {
                $profilePhotoUpdated = $this->accountService->updateProfilePhoto($photo);
            }

            $this->accountService->updateAboutMe($this->request->getParameter('about-me'));
        }

        $profile = $this->profileStore->loadProfile($this->user->id);

        $view = view('@frontend/profile');
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
        $article = $this->articlesFinder->getById($id);

        $this->responseCache->cache();

        $view = view('@frontend/blog/article');
        $view->slug = $slug;
        $view->article = $article;
        $view->comments = $comments;
        $view->settings = $this->settings;
        $view->js_format = $this->i18n->culture->dateTimeFormat;

        return $view;
    }
}
