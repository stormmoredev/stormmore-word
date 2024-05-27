<?php

namespace backend;

use authentication\validators\RepeatedPasswordValidator;
use authentication\validators\UniqueUsernameValidator;
use authentication\AuthenticationService;
use Controller, Route, Request, Response, Form, Redirect, View;

import('@/backend/users/*');

#[Controller]
#[Route("/admin/users")]
readonly class UserController
{
    public function __construct(
        private Request $request,
        private Response $response,
        private UserFinder $userFinder,
        private UserService $userService,
        private AuthenticationService $authenticationService,
        private UserStorage $userStore
    ) { }

    function index()
    {
        $viewData = [];
        $viewData['users'] = $this->userFinder->find();

        return view("@backend/users/index", $viewData);
    }

    function add(): View|Redirect
    {
        $form = new Form($this->request);
        $form->rules = [
            'name' =>       ['required', 'unique_username'],
            'first_name' => ['required'],
            'last_name' =>  ['required'],
            'email' =>      ['required', 'email'],
            'role' =>       ['required', 'option' => ['reader','editor', 'administrator']],
            'password' =>   ['required'],
            'password2' =>  ['required', RepeatedPasswordValidator::class]];
        if ($this->request->isPost() && $form->validate()->isValid()) {
            $user = $this->request->toObject();
            $this->userService->add($user);
            return redirect('/admin/users');
        }

        return view("@backend/users/add", ['form' => $form]);
    }

    function edit(): View|Redirect
    {
        $uid = $this->request['user-id'];
        $user = $this->userStore->find($uid);
        $form = new Form($this->request, $user);
        $form->rules = [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'role' => ['required', 'option' => ['reader' ,'editor', 'administrator']]];
        if ($this->request->isPost() && $form->validate()->isValid()) {
            $this->request->assign($user);
            $this->userService->update($user);
            $this->response->addFlashMessage('success', "User $user->name updated.");
            return redirect('/admin/users');
        }

        return view("@backend/users/edit", ['uid' => $uid, 'form' => $form]);
    }

    #[Route('resend-confirmation-email')]
    function resendConfirmationEmail(): Redirect
    {
        $uid = $this->request->getParameter('user-id');
        $this->authenticationService->sendConfirmationEmail($uid);
        $this->response->addFlashMessage('success', _("Confirmation email sent successfully!"));
        return redirect(url('/admin/users/edit', ['user-id' => $uid]));
    }
}
