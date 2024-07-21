<template x-for="AuthenticationModalComponent">
    <div class="modal-background" x-direct-click="close">
        <div class="modal-body">
            <div x-if="showSigninForm">
                <div class="sm:mx-auto sm:w-full sm:max-w-sm mb-7">
                    <h2 class="mt-10 text-center text-2xl font-bold leading-9 font-light text-gray-900">
                        Sign in to {{ $settings->name }} account
                    </h2>
                </div>
                <div class="rounded-md bg-red-50 p-4 mb-5" x-if="loginFailed">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06
                            1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06
                            10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Sign in failed!</h3>
                            <div class="mt-2 text-sm text-red-700">
                                {{ _ Password is incorrect or email doesn't exist }}
                            </div>
                        </div>
                    </div>
                </div>
                <form action="<?php echo url('/signin-ajax') ?>" method="POST" class="signin-form">
                    <div class="row">
                        <label for="email"><?php echo _('email') ?></label>
                        <input x-disabled="disabled" x-bind="email" id="email" name="email" type="text" required>
                    </div>
                    <div class="row">
                        <label for="password"><?php echo _('password') ?></label>
                        <input x-disabled="disabled" x-bind="password" id="password" name="password" type="password" required>
                    </div>
                    <div class="mt-2 inline-flex items-center">
                        <input type="checkbox" name="remember" value="true" />
                        <label for="remember" class="text-sm ml-2 mt-0 !font-light">
                            <?php echo _('remember_me') ?>
                        </label>
                    </div>

                    <button x-disabled="disabled" x-click="submit" class="mt-5 btn w-full">
                        <?php echo _('sign_in') ?>
                    </button>

                    @component AuthenticationProviders

                    <p class="mt-10 text-center text-sm text-gray-500">
                        Not a member?
                        <a x-click="gotoSignupForm" href="/signup"
                           class="font-semibold leading-6 text-sky-600 hover:text-sky-500">
                            {{ _ Sign up }}
                        </a>
                    </p>
                </form>
            </div>
            <div x-if="showSignupForm">
                <div>
                    <a x-click="gotoSigninForm" class="font-semibold leading-6 text-sky-600 hover:text-sky-500">Rejestracji formularze</a>
                </div>
            </div>

        </div>
    </div>
</template>