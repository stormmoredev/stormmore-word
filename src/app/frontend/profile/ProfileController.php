<?php

/** @noinspection PhpUnused */

namespace app\frontend\profile;

use app\authentication\StormUser;
use app\frontend\profile\domain\ProfileRepository;
use app\frontend\profile\domain\ProfileService;
use app\frontend\profile\presentation\ProfileFinder;
use Controller;
use infrastructure\settings\Settings;
use Request;
use Route;
use View;
use Authenticate;

#[Controller]
readonly class ProfileController
{
    public function __construct(
        private StormUser         $user,
        private Request           $request,
        private Settings          $settings,
        private ProfileService    $accountService,
        private ProfileRepository $profileRepository,
        private ProfileFinder     $profileFinder)
    { }

    #[Route("/me")]
    #[Authenticate]
    public function my(): View
    {
        $profilePhotoUpdated = null;
        if ($this->request->isPost()) {
            $photo = $this->request->getFile('profile-photo');
            if ($photo?->isUploaded()) {
                $profilePhotoUpdated = $this->accountService->updateProfilePhoto($photo);
            }

            $this->accountService->updateAboutMe($this->request->get('about-me'));
        }

        $profile = $this->profileFinder->findMyProfile($this->user->id);

        return view('@frontend/profile/my',[
            'profileUpdated' => $profilePhotoUpdated,
            'profile' => $profile,
            'settings' => $this->settings,
            'maxFileSize' => $this->settings->upload->maxFileSize,
            'maxPhotoSize' => $this->settings->upload->maxPhotoSize,
        ]);
    }

    #[Route("/profile/:slug")]
    public function profile(): View
    {
        $slug = $this->request->get('slug');
        return view('@frontend/profile/profile',['slug' => $slug]);
    }
}