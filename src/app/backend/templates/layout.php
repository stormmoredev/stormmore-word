<?php

include resolve_path_alias('@backend/functions.php');

use app\authentication\StormUser;

$user = di(StormUser::class);
$request = di(Request::class);
$items = [
    ['url' => "/admin/articles", 'title' =>  _("Articles")],
    "/admin/users" => _("Users"),
    "/admin/sessions" => _("Sessions"),
    "/admin/settings" => _("Settings")];

?>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Storm CMS - Backend</title>
    <link rel="icon" type="image/x-icon" href="{{ url('/public/images/storm-cms.ico') }}">

    <script src="/public/editor/medium-editor.min.js"></script>
    <script src="/public/editor/medium-editor-multi-placeholder.min.js"></script>
    <link rel="stylesheet" href="/public/editor/medium-editor.min.css" />
    <link rel="stylesheet" href="/public/editor/medium-editor.theme.css" />

    <link href="/public/main.css" rel="stylesheet">
</head>
<body class="leading-none">
    <div class="fixed inset-y-0 lg:z-50 lg:flex w-72 flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6">
            <div class="flex h-16 shrink-0 items-center">
                <a href="/">
                    <img class="h-14" src="{{ url('/public/images/storm-cms.png') }}" />
                </a>
            </div>
            <nav class="flex flex-1 flex-col">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2 space-y-1">
                            <li>
                                <a href="{{ url('/admin/articles') }}" class="group flex gap-x-3 rounded-md p-2 text-sm
                                    font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-sky-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    {{ _ Articles }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/forum') }}" class="group flex gap-x-3 rounded-md p-2 text-sm
                                    font-semibold leading-6 text-gray-700 hover:text-sky-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                    </svg>
                                    {{ _ Forum }}
                                </a>
                                <ul class="">
                                    <li>
                                        <a href="{{ url('/admin/forum/threads') }}" class="group flex rounded-md p-2 pl-11
                                            text-xs font-semibold leading-6 text-gray-700
                                            hover:bg-gray-50 hover:text-sky-500">
                                            {{ _ Threads }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ url('/admin/forum/categories') }}" class="group flex rounded-md p-2 pl-11
                                            text-xs font-semibold leading-6 text-gray-700
                                            hover:bg-gray-50 hover:text-sky-500">
                                            {{ _ Categories }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ url('/admin/users') }}" class="group flex gap-x-3 rounded-md p-2 text-sm
                                    font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-sky-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    {{ _ Users }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/sessions') }}" class="group flex gap-x-3 rounded-md p-2 text-sm
                                    font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-sky-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    {{ _ Sessions }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/settings') }}" class="group flex gap-x-3 rounded-md p-2 text-sm
                                    font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-sky-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    {{ _ Settings }}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <main class="lg:pl-72">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="lg:mx-auto pb-10">
                <div class="flex h-16 items-center gap-x-4 bg-white px-4 sm:gap-x-6
                        sm:px-6 lg:px-0">
                    <div class="flex flex-1 gap-x-4 self-center lg:gap-x-6">
                        <form class="relative flex flex-1 pt-4" action="#" method="GET">
                            <label for="search-field" class="sr-only">Search</label>
                            <svg class="pointer-events-none absolute top-2 left-0 h-full w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                            <input id="search-field" class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                                   placeholder="Search..." type="search" name="search">
                        </form>
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>
                            <div class="relative group/menu">
                                <button type="button" class="-m-1.5 flex items-center p-1.5">
                                    <span class="sr-only">Open user menu</span>
                                    @if ($user->hasPhoto())
                                    <img class="h-8 w-8 rounded-full bg-gray-50" src="{{ url('/media/profile/' . $user->photo); }}" alt="">
                                    @else
                                    <div id="profile-initials" class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-gray-500 cursor-pointer">
                                        <span class="text-xl font-medium leading-none text-white">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    @end
                                    <span class="hidden lg:flex lg:items-center">
                                      <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                      </svg>
                                    </span>
                                </button>
                                <div class="absolute right-0 z-10">
                                    <div class="mt-2.5 w-32 rounded-md bg-white py-2
                            shadow-lg ring-1 ring-gray-900/5 focus:outline-none hidden group-hover/menu:block">
                                        <a href="/signout" class="block px-3 py-1 text-sm leading-6 text-gray-900" role="menuitem" tabindex="-1" id="user-menu-item-1">Sign out</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                @template
            </div>
        </div>
    </main>
</body>
</html>

