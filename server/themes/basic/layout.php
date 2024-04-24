<?php
    use infrastructure\settings\Settings;
    $settings = di(Settings::class);
    $i18n = di(I18n::class);
    $user = di(\authentication\StormUser::class);
?>

<html class="h-full antialiased">
<head>
    <title>Storm CMS</title>
    <script type="text/javascript" src="public/script.js"></script>
    <link rel="icon" type="image/x-icon" href="/public/images/storm-cms.ico">
    <link href="/public/main.css" rel="stylesheet">
</head>

<body class="flex h-full bg-zinc-50 dark:bg-black">
    <input type="hidden" id="max-file-size" value="{{ $settings->upload->maxFileSize }}"/>
    <input type="hidden" id="max-photo-size" value="{{ $settings->upload->maxPhotoSize }}"/>`
    <div class="flex w-full flex-col">
        <div class="mx-auto w-full max-w-7xl lg:px-8">
            <div class="sm:px-8">
                <div class="px-4 sm:px-8 lg:px-12">
                    <div class="mx-auto max-w-2xl lg:max-w-5xl">
                        <a class="" href="/">
                            <img class="absolute top-0 mt-2 h-16 z-10" src="/public/images/storm-cms.png" />
                        </a>
                        <div class="flex justify-end m-5 text-sm font-semibold leading-6">
                            <div id="user-authenticated"></div>
                            @if ($settings->authentication->enabled)
                            <div id="user-anonymous" class="hidden text-sky-600 hover:text-sky-500">
                                <a href="/signin">{{ _ Sign in }}</a>
                            </div>
                            @end
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full flex-col">
            <main class="flex-auto">
                <div class="sm:px-8 mt-8">
                    <div class="mx-auto w-full max-w-7xl lg:px-8">
                        <div class="px-4 sm:px-8 lg:px-12">
                            @template
                        </div>
                    </div>
                </div>
            </main>
            <footer class="mt-32 flex-none">
                <div class="sm:px-8">
                    <div class="mx-auto w-full max-w-7xl lg:px-8">
                        <div class="border-t border-zinc-100 pb-16 pt-10 dark:border-zinc-700/40">
                            <div class="px-4 sm:px-8 lg:px-12">
                                <div class="mx-auto max-w-2xl lg:max-w-5xl">
                                    <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                                        <div class="flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm font-medium
                                                text-zinc-800 dark:text-zinc-200">
                                            <a class="transition hover:text-teal-500"
                                               href="http://www.stormmore.com/">Stormmore</a>
                                            <a class="transition hover:text-teal-500"
                                               href="http://www.stormmore.com/">Stormmore Framework</a>
                                            <a class="transition hover:text-teal-500"
                                               href="http://www.stormmore.com/">Stormmore Word</a>
                                        </div>
                                        <p class="text-sm text-zinc-400 dark:text-zinc-500">
                                            © <!-- -->2024<!-- --> Storm cloud CMS. All rights reserved.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>

<template id="user-authenticated-template">
    <div class="flex group/menu relative">
        <img id="profile-photo" class="hidden h-10 w-10 rounded-md" />
        <div id="profile-initials" class="hidden inline-flex h-10 w-10 items-center justify-center
            rounded-md bg-gray-500 cursor-pointer">
            <span class="text-xl font-medium leading-none text-white">%username%</span>
        </div>

        <div class="hidden group-hover/menu:block absolute top-10 z-10 right-0 w-48">
            <div class="mt-2 bg-white origin-top-right rounded-md py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                <a href="/admin" id="panel" class="block px-4 py-2 text-gray-600 hover:text-sky-700 hidden">
                    {{ _ Panel }}
                </a>
                <a href="/profile" class="block px-4 py-2 text-gray-600 hover:text-sky-700">
                    {{ _ Profile }}
                </a>
                <a href="/signout" class="block px-4 py-2 text-sm text-gray-600 hover:text-sky-700">
                    {{ _ Sign out }}
                </a>
            </div>
        </div>
    </div>
</template>