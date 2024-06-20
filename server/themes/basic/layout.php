<?php

use app\authentication\StormUser;
use infrastructure\settings\Settings;

$settings = di(Settings::class);
    $i18n = di(I18n::class);
    $user = di(StormUser::class);
?>

<html class="h-full antialiased">
<head>
    <title>Stormmore Word</title>
    <script type="text/javascript" src="{{ url('public/script.js') }}"></script>
    <script type="text/javascript" src="{{ url('public/shared.js') }}"></script>
    <link rel="icon" type="image/x-icon" href="{{ url('/public/images/storm-cms.ico') }}">
    <link href="{{ url('/public/main.css') }}" rel="stylesheet">
</head>

<body class="bg-zinc-50 dark:bg-black">
    <div class="flex flex-col container mx-auto bg-white max-w-7xl px-10" style="min-height: 100%">
        <header class="flex justify-between">
            <a class="" href="{{ url('/') }}">
                <img class="absolute top-0 mt-2 h-16 z-10"  src="{{ url('/public/images/storm-cms.png') }}" />
            </a>
            <nav class="pointer-events-auto hidden md:block pt-5">
                <ul class="flex rounded-full bg-white/90 px-3 text-sm font-medium text-zinc-800 shadow-lg
                            shadow-zinc-800/5 ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-800/90
                            dark:text-zinc-200 dark:ring-white/10">
                    <li>
                        <a class="relative block px-3 py-2 transition hover:text-teal-500
                                    dark:hover:text-teal-400" href="/">{{ _ Blog }}</a>
                    </li>
                    <li>
                        <a class="relative block px-3 py-2 transition hover:text-teal-500
                                    dark:hover:text-teal-400" href="{{ url('/c') }}">{{ _ Community }}</a>
                    </li>
                    <li>
                        <a class="relative block px-3 py-2 transition hover:text-teal-500
                                    dark:hover:text-teal-400" href="{{ url('/f') }}">{{ _ Forum }}</a>
                    </li>
                </ul>
            </nav>
            <div class="flex justify-end my-5 text-sm font-semibold leading-6">
                <div id="user-authenticated"></div>
                @if ($settings->authentication->enabled)
                <div id="user-anonymous" class="hidden text-sky-600 hover:text-sky-500">
                    <a href=" {{ url('/signin') }} ">{{ _ Sign in }}</a>
                </div>
                @end
            </div>
        </header>
        <main class="flex-1 mt-12">@template</main>
        <footer class="w-full">
            <div class="sm:px-8">
                <div class="mx-auto w-full max-w-7xl lg:px-8">
                    <div class="border-t border-zinc-100 pb-16 pt-10 dark:border-zinc-700/40">
                        <div class="px-4 sm:px-8 lg:px-12">
                            <div class="mx-auto max-w-2xl lg:max-w-5xl">
                                <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                                    <div class="flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm font-medium
                                            text-zinc-800 dark:text-zinc-200">
                                        <a class="transition hover:text-teal-500"
                                           href="https://www.stormmore.com/">Stormmore</a>
                                        <a class="transition hover:text-teal-500"
                                           href="https://www.stormmore.com/">Stormmore Framework</a>
                                        <a class="transition hover:text-teal-500"
                                           href="https://www.stormmore.com/">Stormmore Word</a>
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

    <input type="hidden" id="max-file-size" value="{{ $settings->upload->maxFileSize }}"/>
    <input type="hidden" id="max-photo-size" value="{{ $settings->upload->maxPhotoSize }}"/>

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

    {{ js::i18n([
        'date_interval_y_singular',
        'date_interval_y_plural',
        'date_interval_m_singular',
        'date_interval_m_plural',
        'date_interval_d_singular',
        'date_interval_d_plural',
        'date_interval_h_singular',
        'date_interval_h_plural',
        'date_interval_i_singular',
        'date_interval_i_plural',
        'date_interval_seconds_ago'], 'dateDifferenceI18n'); }}
</body>
</html>