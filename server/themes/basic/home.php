@layout @frontend/layout.php

<header class="max-w-2xl">
    <h1 class="text-2xl font-bold tracking-tight text-zinc-800 sm:text-5xl dark:text-zinc-100">
    </h1>
</header>

<div class="mx-auto max-w-2xl lg:max-w-5xl">
    <header class="max-w-2xl">
        <h1 class="text-4xl font-bold tracking-tight text-zinc-800 sm:text-5xl dark:text-zinc-100">
            Storm cloud cms. Platform to share word with the world.
        </h1>
        <p class="mt-6 text-base text-zinc-600 dark:text-zinc-400">
            All of my experience with development and searching for information put into
            <a href="https://github.com/michalczerski/storm-framework-php">framework</a> and finally in platform to share
            a word which is base for knowledge.
        </p>
    </header>
    <div class="mt-16 sm:mt-20">
        <div class="md:border-l md:border-gray-100 md:pl-6 md:dark:border-zinc-700/40">
            <div class="flex max-w-3xl flex-col space-y-16">
                @foreach($articles as $article)
                <article class="md:grid md:grid-cols-3 md:items-baseline">
                    <div class="md:col-span-3 group relative flex flex-col items-start">
                        <h2 class="text-base font-semibold tracking-tight text-zinc-800 dark:text-zinc-100">
                            <div class="absolute -inset-x-4 -inset-y-6 z-0 scale-95 opacity-0 transition bg-white
                                group-hover:scale-100 group-hover:opacity-100 shadow-md
                                sm:-inset-x-6 sm:rounded-2xl dark:bg-zinc-800/50">
                            </div>
                            <a href="{{ url($article->slug) }}">
                                <span class="absolute -inset-x-4 -inset-y-6 z-20 sm:-inset-x-6 sm:rounded-2xl"></span>
                                <span class="relative z-10">{{ $article->title }}</span>
                            </a>
                        </h2>
                        <time class="md:hidden relative z-10 order-first mb-3 flex items-center text-sm text-zinc-400
                            pl-3.5">
                            <span class="absolute inset-y-0 left-0 flex items-center" aria-hidden="true">
                                <span class="h-4 w-0.5 rounded-full bg-zinc-200 dark:bg-zinc-500"></span>
                            </span>{{ $article->published_at }}
                        </time>
                        <p class="relative prose z-10 mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $article->content }}
                        </p>
                        <div aria-hidden="true" class="relative z-10 mt-4 flex items-center
                            text-sm font-medium text-teal-500">{{ _ Read article }}
                            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" class="ml-1 h-4 w-4 stroke-current">
                                <path d="M6.75 5.75 9.25 8l-2.5 2.25" stroke-width="1.5"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"></path>
                            </svg>
                        </div>
                    </div>
                </article>
                @end
            </div>
        </div>
    </div>
</div>