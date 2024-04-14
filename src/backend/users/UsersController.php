<?php

namespace backend;

use authentication\AuthenticationService;
use Controller, Route, Request, Response, Form;

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
        private UserStore $userStore
    ) { }

    function index()
    {
        $viewData = [];
        $viewData['users'] = $this->userFinder->find();

        return view("@backend/users/index", $viewData);
    }

    function add()
    {
        $form = new Form($this->request);
        $form->rules = [
            'name' => 'required unique_username',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required email',
            'role' => 'required option:reader:editor:administrator',
            'password' => 'required',
            'password2' => 'required repeat_password'];
        if ($this->request->isPost() && $form->validate()->isValid()) {
            $user = $this->request->toObject();
            $this->userService->add($user);
            $this->response->redirect('/admin/users');
        }

        return view("@backend/users/add", ['form' => $form]);
    }

    function edit()
    {
        $uid = $this->request['user-id'];
        $user = $this->userStore->find($uid);
        $form = new Form($this->request, $user);
        $form->rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required option:reader:editor:administrator'];
        if ($this->request->isPost() && $form->validate()->isValid()) {
            $this->request->bind($user);
            $this->userService->update($user);
            $this->response->addFlashMessage('success', "User $user->name updated.");
            $this->response->redirect('/admin/users');
        }

        return view("@backend/users/edit", ['uid' => $uid, 'form' => $form]);
    }

    #[Route('resend-confirmation-email')]
    function resendConfirmationEmail(): void
    {
        $uid = $this->request->getParameter('user-id');
        $this->authenticationService->sendConfirmationEmail($uid);
        $this->response->addFlashMessage('success', _("Confirmation email sent successfully!"));
        $this->response->redirect(url('/admin/users/edit', ['user-id' => $uid]));
    }
}

