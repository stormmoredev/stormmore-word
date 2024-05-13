<?php

namespace frontend;

use Authenticate;
use authentication\StormUser;
use Controller;
use frontend\account\ProfileService;
use frontend\account\ProfileStorage;
use frontend\comments\CommentFinder;
use infrastructure\settings\Settings;
use infrastructure\Slug;
use Response, ResponseCache;
use Request;
use I18n;
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

    #[Route('/upload')]
    public function upload(): View
    {
        $directory = resolve_path_alias("@profile/");
        if ($this->request->isPost()) {
            $file = $this->request->getFile('file');
            if ($file?->isUploaded()) {
                $file->move($directory, ['gen-unique-filename' => true]);
            }
        }
        return view("@frontend/upload");
    }

    #[Route("/:slug")]
    public function article(): View
    {
        $slug = $this->request->getParameter('slug');
        list($id) = $this->slug->getParameters($slug);

        $comments = $this->commentFinder->find($id);
        $article = $this->articlesFinder->findOne($id);

        $this->responseCache->cache();

        $view = view('@frontend/article');
        $view->slug = $slug;
        $view->article = $article;
        $view->comments = $comments;
        $view->settings = $this->settings;
        $view->js_format = $this->i18n->culture->dateTimeFormat;

        return $view;
    }
}
