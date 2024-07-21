<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>{{ $settings->name }}</title>
    <link href="<?php echo url('/public/main.css') ?>" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?php echo url('/public/storm-cms.ico') ?>">
</head>

<body class="d-flex align-items-center py-2 bg-body-tertiary">
<div class="flex min-h-full flex-col justify-center px-6 py-7 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <a href="<?php echo url('/') ?>">
            <img class="mx-auto  w-auto" src="{{ url('/public/images/storm-cms.png') }}" alt="Storm CMS">
        </a>
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Sign in to
            {{ $settings->name }} account
        </h2>
    </div>

    <div class="mt-7 sm:mx-auto sm:w-full sm:max-w-sm">
        @if ($confirmStatus === true)
        <div class="rounded-md bg-green-50 p-4 mb-5">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0
                        00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                              clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Thank you!</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Your account is activated and you can sign in.</p>
                    </div>
                </div>
            </div>
        </div>
        @end
        @if ($confirmStatus === false)
        <div class="rounded-md bg-red-50 p-4 mb-5">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06
                            1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06
                            10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">{{ _ Email confirmation failed ! }}</h3>
                    <div class="mt-2 text-sm text-red-700">
                        {{ _ Activation link expired or it's already confirmed }}
                    </div>
                </div>
            </div>
        </div>
        @end
        @if ($signinFailed)
        <div class="rounded-md bg-red-50 p-4 mb-5">
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
        @end
        <form action="{{ url('/signin', ['redirect' => $redirect]) }}" method="POST" class="signin-form">
            <div class="row">
                <label for="email">{{ _ Email }}</label>
                <input id="email" name="email" type="text"  required>
            </div>
            <div class="row">
                <label for="password">
                    Password
                </label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="mt-2 inline-flex items-center">
                {{ html::checkbox("remember") }}
                <label for="remember" class="text-sm ml-2 mt-0 !font-light">
                    {{ _ Remember me }}
                </label>
            </div>
            <button type="submit" class="mt-5 btn w-full">{{ _ Sign in }}</button>

            @component AuthenticationProviders

            <p class="mt-10 text-center text-sm text-gray-500">
                Not a member?
                <a href="/signup" class="font-semibold leading-6 text-sky-600 hover:text-sky-500"> {{ _ Sign up }}</a>
            </p>

        </form>
    </div>
</div>
</body>
</html>