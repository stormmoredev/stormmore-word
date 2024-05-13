<?php

namespace frontend\account;

use authentication\AuthenticationCookie;
use authentication\StormUser;
use infrastructure\MediaFiles;
use infrastructure\settings\Settings;
use Request, UploadedFile;

readonly class ProfileService
{
    public function __construct(
        private StormUser            $stormUser,
        private MediaFiles           $mediaFiles,
        private ProfileStorage       $accountStore,
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