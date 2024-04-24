<?php

namespace infrastructure\settings;

use Cookies;
use Language;

class Settings
{
    public string $name;
    public $url;
    public $theme;
    public $themes;
    public $roles;
    public string $defaultRole;
    public string $pageSize;
    public string $secretKey;

    public bool $multiLanguage = false;
    public Language $defaultApplicationLanguage;
    public array $applicationLanguages = [];
    public array $enabledLanguages = [];
    public Language $defaultLanguage;

    public function __construct(
        public EditorEntrySettings    $editorEntry = new EditorEntrySettings(),
        public MailSettings           $mail = new MailSettings(),
        public SessionSettings        $session = new SessionSettings(),
        public DatabaseSettings       $database = new DatabaseSettings(),
        public AuthenticationSettings $authentication = new AuthenticationSettings(),
        public CommentsSettings       $comments = new CommentsSettings(),
        public UploadSettings         $upload = new UploadSettings()
    ) { }

    public function setDefaultLanguage($language): void
    {
        $this->defaultLanguage = new Language($language);
    }

    public function setEnabledLanguages($languages): void
    {
        $enabledLanguages = [];
        foreach($languages as $language) {
            $enabledLanguages[] = new Language($language);
        }
        $this->enabledLanguages = $enabledLanguages;
    }

    public function setApplicationLanguages(array $languages): void
    {
        foreach($languages as $language) {
            $this->applicationLanguages[] = new Language($language);
        }
    }

    public function setDefaultApplicationLanguage($language): void
    {
        $this->defaultApplicationLanguage = new Language($language);
    }

    public function getHybridauthConfiguration(): array
    {
        return [
            'callback' => $this->url . "/callback",
            'providers' => [
                'google' => [
                    'enabled' => $this->authentication->google->enabled,
                    'keys' => [
                        'id' => $this->authentication->google->id,
                        'secret' => $this->authentication->google->secret]
                ],
                'facebook' => [
                    'enabled' => $this->authentication->facebook->enabled,
                    'keys' => [
                        'id' => $this->authentication->facebook->id,
                        'secret' => $this->authentication->facebook->secret]
                ],
                'wordpress' => [
                    'enabled' => $this->authentication->wordpress->enabled,
                    'keys' => [
                        'id' => $this->authentication->wordpress->id,
                        'secret' => $this->authentication->wordpress->secret]
                ]
            ]
        ];
    }

    public function getUserLanguage($acceptedLanguages): Language
    {
        if ($this->multiLanguage) {
            if (Cookies::has('user-lang')) {
                $language = new Language(Cookies::get('user-lang'));
                if ($this->isEnabledLanguage($language)) {
                    return $language;
                }
            }

            foreach ($acceptedLanguages as $acceptedLanguage) {
                if ($this->isEnabledLanguage($acceptedLanguage)) {
                    return $acceptedLanguage;
                }
            }
        }

        return $this->defaultLanguage;
    }

    public function getApplicationLanguage($acceptedLanguages): Language
    {
        if ($this->multiLanguage) {
            foreach ($acceptedLanguages as $acceptedLanguage) {
                if ($this->isApplicationLanguage($acceptedLanguage)) {
                    return $acceptedLanguage;
                }
            }
        }

        return $this->defaultApplicationLanguage;
    }

    public function getEnabledLanguagesCodes(): array
    {
        return array_column($this->enabledLanguages, 'primary');
    }

    private function isApplicationLanguage(Language $language): bool {
        foreach ($this->applicationLanguages as $applicationLanguage) {
            if ($applicationLanguage->equals($language)) {
                return true;
            }
        }

        return false;
    }
    private function isEnabledLanguage(Language $language): bool {
        foreach ($this->enabledLanguages as $enabledLanguage) {
            if ($enabledLanguage->equals($language)) {
                return true;
            }
        }

        return false;
    }
}