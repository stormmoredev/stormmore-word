<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>{{ $settings->name }}</title>
    <link href="/public/main.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/public/storm-cms.ico">
</head>

<body class="d-flex align-items-center py-2 bg-body-tertiary">
<div class="flex min-h-full flex-col justify-center px-6 py-7 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <a href="/"><img class="mx-auto  w-auto" src="/public/storm-cms.png" alt="Storm CMS"></a>
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Sign up to {{ $settings->name }}
        </h2>
    </div>
    @if ($signupSuccess)
    <div class="rounded-md bg-green-50 p-4 mt-7 sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">{{ _ Thank you for signing up!  }}</h3>
                <div class="mt-2 text-sm text-green-700">
                    <p>{{ _ Confirm your e-mail address by clicking activation link sent to  %s | $form->getValue('email') }}</p>
                </div>
                <div class="mt-4">
                    <div class="-mx-2 -my-1.5 flex">
                        <a href="/signin"
                                class="rounded-md bg-green-50 px-2 py-1.5 text-sm font-medium text-green-800
                                hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600
                                focus:ring-offset-2 focus:ring-offset-green-50">
                            Sign in
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @end
    <div class="mt-7 sm:mx-auto sm:w-full sm:max-w-sm">
        <form action="/signup" method="POST" class="signup-form">
            <div class="row">
                {{ $form->label("name", "Username") }}
                {{ $form->text("name") }}
                {{ $form->error("name") }}
            </div>
            <div class="row">
                {{ $form->label("email", "Email") }}
                {{ $form->text("email") }}
                {{ $form->error("email") }}
            </div>
            <div class="row">
                {{ $form->label("password", "Password") }}
                {{ $form->password("password") }}
                {{ $form->error("password") }}
            </div>
            <div class="row">
                {{ $form->label("password2", "Password") }}
                {{ $form->password("password2") }}
                {{ $form->error("password2") }}
            </div>
            <button type="submit" class="mt-5 btn w-full">{{ _ Sign up }}</button>
        </form>

        <div>
            <p class="mt-10 text-center text-sm text-gray-500">
                {{ _ Already have an account ? }}
                <a href="/signin" class="font-semibold leading-6 text-sky-600 hover:text-sky-500"> {{ _ Sign in }}</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>