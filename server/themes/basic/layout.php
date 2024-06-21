<html class="h-full antialiased">
<head>
    <title>Stormmore CommunityWord</title>
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
                @component ModulePanel
            <div class="flex justify-end my-5 text-sm font-semibold leading-6">
                <div id="user-authenticated"></div>
                @component UserPanel
            </div>
        </header>
        <main class="flex-1 mt-12">@template</main>
        <footer class="w-full mt-24">
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
    @component Settings
</body>
</html>