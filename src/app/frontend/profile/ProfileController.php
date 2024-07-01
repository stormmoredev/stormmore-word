<?php

namespace app\frontend\profile;

use app\authentication\StormUser;
use Controller;
use infrastructure\settings\Settings;
use Request;
use Route;
use View;

#[Controller]
class ProfileController
{
    public function __construct(
        private StormUser      $user,
        private Request        $request,
        private Settings       $settings,
        private ProfileService $accountService,
        private ProfileStorage $profileStore,)
    { }

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

        return view('@frontend/profile',[
            'profileUpdated' => $profilePhotoUpdated,
            'profile' => $profile,
            'settings' => $this->settings,
            'maxFileSize' => $this->settings->upload->maxFileSize,
            'maxPhotoSize' => $this->settings->upload->maxPhotoSize,
        ]);
    }
}