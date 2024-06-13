@layout @frontend/layout.php

<div class="mx-auto max-w-2xl lg:max-w-5xl">
    <form action="{{ url('/f/add-thread') }}" method="post" class="relative">
        <div class="overflow-hidden rounded-lg border border-gray-300 shadow-sm pb-14">
            <label for="title" class="sr-only">Title</label>
            <input type="text" name="title" id="title"
                   class="block w-full border-0 pt-2.5 text-lg font-medium placeholder:text-gray-400 focus:ring-0"
                   placeholder="{{ _ Title }}" onkeydown="forumEntry.onTitleKeydown(event)">
            <label for="description" class="sr-only">Description</label>
            <textarea type="text" rows="1" name="content" id="content" oninput="forumEntry.onContentChange(this)"
                   class="block w-full resize-none border-0 py-0 text-gray-900 placeholder:text-gray-400 focus:ring-0
                            sm:text-sm sm:leading-6 min-h-14 overflow-hidden"
                      placeholder="{{ _ Write a description... }}"></textarea>
        </div>
        <div class="absolute inset-x-px bottom-0">
            <div class="flex items-center justify-between space-x-3 border-t border-gray-200 px-2 py-2 sm:px-3">
                <div class="flex"></div>
                <div class="flex-shrink-0">
                    <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 px-3 py-2 text-sm
                        font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline focus-visible:outline-2
                        focus-visible:outline-offset-2 focus-visible:outline-sky-600">Create</button>
                </div>
            </div>
        </div>
    </form>
</div>