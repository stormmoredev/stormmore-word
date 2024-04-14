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
    <link rel="icon" type="image/x-icon" href="/public/storm-cms.ico">
    <link href="/public/main.css" rel="stylesheet">
</head>

<body class="flex h-full bg-zinc-50 dark:bg-black">
    <div class="flex w-full flex-col">
        <div class="mx-auto w-full max-w-7xl lg:px-8">
            <div class="flex justify-end px-4 sm:px-8 lg:px-12">
                <div class="flex justify-end m-5 text-sm font-semibold
                        leading-6 text-sky-600 hover:text-sky-500">
                    <div id="user-authenticated"></div>
                    @if ($settings->authentication->enabled)
                    <div id="user-anonymous" class="hidden">
                        <a href="/signin">{{ _ Sign in }}</a>
                    </div>
                    @end
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
                                        <div class="flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                            <a class="transition hover:text-teal-500" href="http://www.stormmore.com/">Storm</a>
                                            <a class="transition hover:text-teal-500" href="http://www.stormmore.com/">Framework</a>
                                            <a class="transition hover:text-teal-500" href="http://www.stormmore.com/">Cloudword</a>
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
    <a href="/signout">{{ _ Sign out }}</a>
</template>