<div class="flex justify-end my-5 text-sm font-semibold leading-6">
    <div id="user-authenticated"></div>
    @if ($settings->authentication->enabled)
    <div id="user-anonymous" class="hidden text-sky-600 hover:text-sky-500">
        <a href=" {{ url('/signin') }} ">{{ _ Sign in }}</a>
    </div>
    @end
</div>
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