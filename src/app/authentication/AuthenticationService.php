<?php

namespace app\authentication;

use DateTime;
use Hybridauth\Hybridauth;
use Hybridauth\User\Profile;
use infrastructure\MailNotifications;
use infrastructure\session\SessionHash;
use infrastructure\settings\Settings;
use SessionStorage;
use stdClass;

import ('@vendor/hybridauth/src/autoload');

readonly class AuthenticationService
{
    public Hybridauth $hybridauth;
    public HybridauthCookieStorage $hybridAuthStorage;

    function __construct (
        private SessionStorage      $sessionStore,
        private UserRepository      $userRepository,
        private UserTokenRepository $userTokenRepository,
        private Settings            $settings,
        private UserSecret          $userSecret,
        private MailNotifications   $mailNotifications
    )
    {
        $hybridAuthConfig = $this->settings->getHybridauthConfiguration();
        $this->hybridAuthStorage = new HybridauthCookieStorage();
        $this->hybridauth = new Hybridauth($hybridAuthConfig, storage:  $this->hybridAuthStorage);
    }

    public function signInByEmail($email, $password, $remember = false): array
    {
        if (empty($email) or empty($password) or !str_contains($email, '@'))
            return array(false, false);

        $password = PasswordHash::hash($password);
        $user = $this->userRepository->getByEmailAndPassword($email, $password);
        if ($user == null or !$user->is_activated) return false;

        $lifeTime = $this->settings->session->sessionLifeTime;
        $rememberLifeTime = $this->settings->session->rememberSessionLifeTime;
        $validationTimeSpan = $remember ? $lifeTime : $rememberLifeTime;
        $now = new DateTime();
        $validTo = $now->modify($validationTimeSpan);
        return [$this->sessionStore->create($user->id, $validTo, $remember), $user];
    }

    public function signIn(stdClass $user): string
    {
        $lifeTime = $this->settings->session->rememberSessionLifeTime;
        $now = new DateTime();
        $validTo = $now->modify($lifeTime);
        return $this->sessionStore->create($user->id, $validTo, true);
    }

    public function signUp(stdClass $user): void
    {
        $user->password = PasswordHash::hash($user->password);
        $user->role = $this->settings->defaultRole;
        $user->id = $this->userRepository->create($user);
    }

    public function createAccount(Profile $profile, string $username): stdClass
    {
        $user = new stdClass();
        $user->name = $username;
        $user->role = $this->settings->defaultRole;
        $user->email = $profile->email;
        $user->password = null;
        $user->id = $this->userRepository->create($user);

        $this->userRepository->activate($user->id);

        return $user;
    }

    /**
     * @throws \Exception
     */
    public function confirm($key): bool
    {
        $now = new DateTime();
        $key = $this->userSecret->decrypt($key);
        $token = $this->userTokenRepository->getByKey($key);
        if ($token == null or $token->valid_to < $now) return false;
        $this->userTokenRepository->delete($key);
        $this->userRepository->activate($token->user_id);

        return true;
    }

    public function sendConfirmationEmail($uid): void
    {
        $nextWeek = new DateTime("+7 days");
        $user = $this->userRepository->getById($uid);
        $token = $this->userTokenRepository->create($user->id, $nextWeek);
        $token = $this->userSecret->encrypt($token);
        $this->mailNotifications->signupConfirmation($user->email, $token);
    }
}