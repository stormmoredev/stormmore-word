@layout @frontend/layout.php

<div class="bg-slate-50">
    <form action="{{ url('/b/add-post') }}" method="post" class="relative">
        <div class="overflow-hidden rounded-lg border border-gray-300 shadow-sm pb-14">
            <input type="text" name="title" id="title"
                   class="block w-full border-0 text-lg font-medium pb-1 mb-0
                    placeholder:text-gray-400 focus:ring-0" maxlength="128"
                   placeholder="{{ _ post_title }}" onkeydown="blogEntry.onTitleKeydown(event)">
            {{ $form->error('title') }}
            <input type="text" name="subtitle" id="subtitle"
                   class="block w-full border-0 text-lg font-light pb-1 mb-0
                    placeholder:text-gray-400 focus:ring-0" maxlength="256"
                   placeholder="{{ _ post_subtitle }}"">
            {{ $form->error('subtitle') }}
            <input type="text" name="media" id="media"
                   class="block w-full border-0 text-sm font-light pt-0 mt-0
                    placeholder:text-gray-400 focus:ring-0 text-gray-400"
                   placeholder="{{ _ post_media_media }}" autocomplete="off">
            {{ $form->error('media') }}
            <textarea type="text" rows="1" name="content" id="content" oninput="blogEntry.onContentChange(this)"
                      class="block w-full resize-none border-0 pt-2 text-gray-900
                        placeholder:text-gray-400 focus:ring-0
                        sm:text-sm sm:leading-6 min-h-96 overflow-hidden"
                      placeholder="{{ _ post_placeholder }}"></textarea>
            {{ $form->error('content') }}
        </div>
        <div class="absolute inset-x-px bottom-0">
            <div class="flex items-center justify-between space-x-3 border-t border-gray-200 px-2 py-2 sm:px-3">
                <div class="flex"></div>
                <div class="flex-shrink-0">
                    <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 px-3 py-2 text-sm
                        font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2
                        focus-visible:outline-offset-2 focus-visible:outline-sky-600">{{ _ post_save }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>