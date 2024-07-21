<?php

namespace app\frontend\profile\domain;

use app\authentication\AuthenticationCookie;
use app\authentication\StormUser;
use infrastructure\MediaFiles;
use infrastructure\settings\Settings;
use Request;
use UploadedFile;

readonly class ProfileService
{
    public function __construct(
        private StormUser            $stormUser,
        private MediaFiles           $mediaFiles,
        private ProfileRepository    $accountStore,
        private AuthenticationCookie $authenticationCookie,
        private Settings             $settings,
        private Request              $request
    )
    {
    }

    public function updateProfilePhoto(UploadedFile $photo): bool
    {
        if (!$photo?->isImage()) return false;
        if ($photo?->exceedSize($this->settings->upload->maxPhotoSize)) return false;

        $email = $this->stormUser->email;
        $name = sha1($email);
        $profilePhotoName = $name . ".jpg";
        $this->mediaFiles->writeProfilePhoto($photo, $profilePhotoName);
        $this->accountStore->updateProfilePhoto($this->stormUser->id, $profilePhotoName);

        $this->authenticationCookie->update('photo', $profilePhotoName);

        return true;
    }

    public function updateAboutMe($aboutMe): void
    {
        $this->accountStore->updateAboutMe($this->stormUser->id, $aboutMe);
    }
}