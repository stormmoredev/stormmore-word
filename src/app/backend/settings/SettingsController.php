<?php

namespace app\backend\settings;

use Controller;
use infrastructure\Languages;
use infrastructure\MailNotifications;
use infrastructure\settings\Settings;
use infrastructure\settings\SettingsFile;
use Language;
use Request;
use Route;
use View;

import ('@/backend/settings/*');

#[Controller]
readonly class SettingsController
{
    public function __construct (
        private Request $request,
        private Settings $settings,
        private SettingsFile $settingsFile,
        private Languages $languages,
        private MailNotifications $mailNotifications,
    ) { }

    #[Route("/admin/settings")]
    public function index(): View
    {
        $roles = [];
        foreach ($this->settings->roles as $role) {
            $roles[$role] = _(ucfirst($role));
        }
        if ($this->request->isPost())
        {
            $this->settings->name = $this->request->name;
            $this->settings->pageSize = $this->request->pageSize;

            $this->settingsFile->save($this->settings);
        }

        return view('@backend/settings/index', [
            'settings' => $this->settings,
            'roles' => $roles]);
    }

    #[Route("/admin/settings/mail")]
    public function mail(): View
    {
        $data = ['settings' => $this->settings];
        return view('@backend/settings/mail', $data);
    }

    #[Route("/admin/settings/comments")]
    public function comments(): View
    {
        if ($this->request->isPost()) {
            $this->settings->comments->enabled = $this->request->getParameter('enabled');
            $this->settingsFile->save($this->settings);
        }
        return view('@backend/settings/comments', ['settings' => $this->settings]);
    }

    #[Route("/admin/settings/languages")]
    public function language(): View
    {
        $list = $this->languages->getList();
        $enabledCodes = $this->settings->getEnabledLanguagesCodes();

        if ($this->request->isPost()) {
            $enabledCodes = $this->request->getParameter('enabled');
            $enabledCodes = array_keys(array_filter($enabledCodes, fn($c) => $c == true));
            $this->settings->setEnabledLanguages($enabledCodes);
            $this->settings->defaultLanguage = new Language($this->request->getParameter('defaultLanguage'));
            $this->settings->multiLanguage = $this->request->getParameter('multiLanguage', false);

            $this->settingsFile->save($this->settings);
        }

        $data = [
            'multiLanguage' => $this->settings->multiLanguage,
            'default' => $this->settings->defaultLanguage,
            'list' => $list,
            'enabledCodes' => $enabledCodes,
            'enabled' => $this->languages->getList($enabledCodes)];

        return view('@backend/settings/languages', $data);
    }

    #[Route("/admin/settings/authentication")]
    public function authentication(): View
    {
        $data = ['settings' => $this->settings];
        return view('@backend/settings/authentication', $data);
    }

    #[Route("/admin/settings/mail/smtp-test")]
    public function mailTest(): View
    {
        $this->mailNotifications->SmtpTest();
        $data = ['settings' => $this->settings];
        return view('@backend/settings/mail', $data);
    }
}