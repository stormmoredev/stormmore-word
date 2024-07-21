<?php

namespace app\authentication;

use Controller;
use Cookies;
use Exception;
use Form;
use Hybridauth\Hybridauth;
use infrastructure\AjaxResult;
use infrastructure\settings\Settings;
use Redirect;
use Request;
use Response;
use Route;
use View;

import ('@vendor/hybridauth/src/autoload');

#[Controller]
readonly class AuthenticationController
{
    private Hybridauth $hybridauth;
    private HybridauthCookieStorage $storage;

    public function __construct(
        private AuthenticationCookie  $authenticationCookie,
        private AuthenticationService $authenticationService,
        private UserRepository        $userRepository,
        private Settings              $settings,
        private Request               $request,
        private Response              $response
    )
    {
        $config = $this->settings->getHybridauthConfiguration();
        $this->storage = new HybridauthCookieStorage();
        $this->hybridauth = new Hybridauth($config, storage:  $this->storage);
    }

    #[Route("/signin")]
    function signin(): View|Redirect
    {
        $this->settings->authentication->enabled or throw new Exception("Signing in is disabled", 500);

        $signInFailed = false;
        if ($this->request->isPost()) {
            list($email, $password) = $this->request->get('email', 'password');
            if ($this->authenticationService->signInByEmail($email, $password)) {
                if ($this->request->hasParameter('redirect')) {
                    return redirect($this->request->decodeParameter('redirect'));
                }
                if (str_ends_with($this->request->getReferer(), '/signin')) {
                    return redirect();
                }
                return back();
            }

            $signInFailed = true;
        }

        return view('@frontend/signin', [
            'settings' => $this->settings,
            'confirmStatus' => null,
            'signinFailed' => $signInFailed,
            'redirect' => $this->request->decodeParameter('redirect')
        ]);
    }

    #[Route("/signin-ajax")]
    function signinAjax(): AjaxResult {
        list($email, $password) = $this->request->get('email', 'password');
        if ($this->authenticationService->signInByEmail($email, $password)) {
            return new AjaxResult(1);
        }
        return new AjaxResult(0);
    }

    #[Route("/signup")]
    function signup(): View
    {
        $this->settings->authentication->enabled or throw new Exception("Signing up is disabled", 500);

        $form = new Form($this->request);
        $data = [
            'form' => $form,
            'settings' => $this->settings,
            'signupSuccess' => false,
        ];
        $form->rules = [
            'name' => 'required authentication\unique_username',
            'email' => 'required email authentication\unique_email',
            'password' => 'required',
            'password2' => 'required authentication\repeat_password'];
        if ($this->request->isPost() && $form->validate()->isValid()) {
            $user = $this->request->toObject();
            $this->authenticationService->signUp($user);
            $this->authenticationService->sendConfirmationEmail($user->id);
            $data['signupSuccess'] = true;
        }
        return view('@frontend/signup', $data);
    }

    #[Route("/signup/:provider")]
    function signByProvider(): void
    {
        $this->settings->authentication->enabled or throw new Exception("Signing up is disabled", 500);

        $providerName = $this->request->getParameter('provider');
        $providerExists = property_exists($this->settings->authentication, $providerName);
        $providerExists or throw new Exception("Provider doesn't exist", 500);
        $provider = $this->settings->authentication->$providerName;
        $provider->enabled or throw new Exception("Provider isn't enabled", 500);
        $this->storage->set('providerName', $providerName);
        $this->hybridauth->authenticate($providerName);
    }

    #[Route("/callback")]
    function signInByProviderCallback(): Redirect
    {
        $providerName = $this->storage->get('providerName');
        $adapter = $this->hybridauth->authenticate($providerName);
        $profile = $adapter->getUserProfile();

        $user = $this->userRepository->getByEmail($profile->email);
        if ($user == null) {
            $username = $this->request->getParameter('username', $profile->displayName);
            $u = $this->userRepository->getByUsername($username);
            if ($u != null) {
                return view('@frontend/signin-username', ['username' => $username]);
            }

            $user = $this->authenticationService->createAccount($profile, $username);
        }

        $sessionKey = $this->authenticationService->signIn($user);
        $this->authenticationCookie->addUser($user, $sessionKey);

        $adapter->disconnect();
        $this->storage->clear();

        return redirect("/");
    }

    #[Route("/confirm-email")]
    function confirm(): View
    {
        $token = $this->request->getParameter('token');
        $confirmed = $this->authenticationService->confirm($token);
        $data = [
            'settings' => $this->settings,
            'confirmStatus' => $confirmed,
            'signinFailed' => false
        ];
        return view('@frontend/signin', $data);
    }

    #[Route("/signout")]
    function logout(): Redirect
    {
        $this->authenticationCookie->delete();
        return redirect("/");
    }

    #[Route("/language")]
    function language(): Redirect
    {
        $userLang = $this->request->getParameter('user-lang');
        if ($userLang == '') {
            Cookies::delete('user-lang');
        } else {
            Cookies::set('user-lang', $userLang);
        }

        return back("/");
    }
}