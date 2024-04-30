@layout @frontend/layout.php

<div class="mx-auto max-w-2xl lg:max-w-5xl">
    <article id="article">
        <header class="flex flex-row justify-between items-center">
            <a href="/" type="button" aria-label="Go back to articles"
               class="flex h-10 w-10 items-center justify-center rounded-full bg-white
                    shadow-md shadow-zinc-800/5 ring-1 ring-zinc-900/5">
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" class="h-4 w-4 stroke-zinc-500">
                    <path d="M7.25 11.25 3.75 8m0 0 3.5-3.25M3.75 8h8.5" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round">
                    </path>
                </svg>
            </a>

            <div class="text-base text-zinc-400 dark:text-zinc-500">
                <time>
                    <span class="ml-3">{{ $article->published_at | date d MMMM  Y }}</span>
                </time>
                <span>| {{ $article->author_name }} </span>
            </div>
        </header>
        <div class="text-zinc-600 prose">
            {{ $article->content }}
        </div>
    </article>

    @if ($settings->comments->enabled)
    <div class="mt-12">
        @if (Flash::isset('comment-success'))
        <div class="mb-7 flex flex-row w-full px-4 py-4 text-base shadow-md
                 text-gray-800 rounded-lg font-regular bg-gray-900/10">
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-green-800">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25
                            17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06
                            1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"></path>
            </svg>
            <span class="ml-5">{{ _ Your comment has been added. }}</span>
        </div>
        @end
        <div id="write-comment"></div>
    </div>
    <template id="write-comment-not-authorized">
        <div class="flex justify-center">
            <a class="font-semibold leading-6 text-sky-600 hover:text-sky-500" href="/signin">
                {{ _ Sign in to comment }}
            </a>
        </div>
    </template>
    <template id="write-comment-authorized">
        <form action="/add-comment/{{ $slug }}" method="post" class="relative">
            <input name="article-id" type="hidden" value="{{ $article->id }} "/>
            <div class="overflow-hidden rounded-lg border border-gray-300 shadow-sm">
                        <textarea name="content" class="block w-full resize-none border-0 p-3
                            h-36 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                                  placeholder=" {{ _ Write your comment ... }} "></textarea>
                <div aria-hidden="true">
                    <div class="h-px"></div>
                    <div class="py-2">
                        <div class="py-px">
                            <div class="h-9"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-px bottom-0">
                <div class="flex items-center justify-between space-x-3 border-t border-gray-200 px-2 py-2 sm:px-3">
                    <div class="flex"></div>
                    <div class="flex-shrink-0">
                        <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 px-3
                                    py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">{{ _ Save }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </template>
    <script type="text/javascript">toggleCommentPanel();</script>
    @end

    @if (count($comments))
        <div class="mt-10">
            @foreach($comments as $comment)
            <div id="comment-{{ $comment->id }}" class="flex mt-5 space-x-4 text-sm">
                <div class="flex-none py-0">
                    {{ profile_photo($comment->author_name, $comment->author_photo) }}
                </div>
                <div class="flex-1 py-0 ">
                    <h3 class="font-medium text-gray-900">{{ $comment->author_name }}</h3>
                    <p class="text-xs convert-to-datetime-diff"
                       data-date="{{ $comment->created_at | js_datetime }}"
                    </p>
                    <div class="text-base mt-2 max-w-none text-gray-700">
                        <p>{{ $comment->content }}</p>
                    </div>
                </div>
            </div>
            @end
        </div>
    @end

</div>