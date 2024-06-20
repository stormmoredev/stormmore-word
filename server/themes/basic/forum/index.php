@layout @frontend/layout.php

<div class="mx-auto flex justify-end">
    <a href="{{ url('/f/add-thread') }}" class="block rounded-md bg-sky-600 px-3 py-2
        text-center text-sm font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline
        focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
        {{ _ Add thread }}
    </a>
</div>
<div class="mt-8 flex">
    <div class="w-72">
        <div class="pb-2 pt-4">
            <div class="space-y-2 flex flex-col">
                @foreach($categories as $category)
                <a href="{{ url("/f/c/$category->id") }}" class="text-sm text-gray-500">{{ $category->name }}</a>
                @end
            </div>
        </div>
    </div>
    <ul role="list" class="divide-y divide-gray-100 flex-1">
        @foreach($threads as $thread)
        <li class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 py-5 sm:flex-nowrap">
            <div>
                <p class="text-sm font-semibold leading-6 text-gray-900">
                    <a href="{{ url ('/f/thread/' . $thread->id) }}" class="hover:underline">{{ $thread->title }}</a>
                </p>
                <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                    <p>
                        <a href="#" class="hover:underline"> {{ $thread->author_name }}</a>
                    </p>
                    <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current">
                        <circle cx="1" cy="1" r="1" />
                    </svg>
                    <p>
                        <time class="convert-to-datetime-diff" data-date="{{ $thread->issued_at | js_datetime }}"></time>
                    </p>
                </div>
            </div>
            <dl class="flex w-full flex-none justify-between gap-x-8 sm:w-auto">
                <div class="flex -space-x-0.5"></div>
                <div class="flex w-16 gap-x-2.5">
                    <dt>
                        <span class="sr-only">Total comments</span>
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994
                                2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443
                                48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </dt>
                    <dd class="text-sm leading-6 text-gray-900">{{ $thread->replies }}</dd>
                </div>
            </dl>
        </li>
        @end
    </ul>
</div>
