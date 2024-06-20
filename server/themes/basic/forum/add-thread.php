@layout @frontend/layout.php

<div class="mx-auto max-w-2xl lg:max-w-5xl">
    <form action="{{ url('/f/add-thread', ['c' => $cid]) }}" method="post" class="relative">

        @if ($form->isInvalid())
            <div>{{ _('Fill all fields') }}</div>
        @end

        <h2 class="text-base font-semibold leading-7 text-gray-900">Profile</h2>
        <p class="mt-1 text-sm leading-6 text-gray-500 mb-3">
            This information will be displayed publicly so be careful what you share.
        </p>

        @if ($selectCategories)
        <div class="relative input-with-combobox mb-2">
            <input type="hidden" name="c" />
            <input id="combobox" type="text" autocomplete="off" placeholder="{{ _ Category }}"
                   class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-12
                        text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300
                        sm:text-sm sm:leading-6 placeholder:text-gray-400 focus:outline-none">
            <button type="button" class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852
                        7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7
                        2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0
                        01.04-1.06z"/>
                </svg>
            </button>
            <ul class="hidden absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base
                shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm py-2"
                id="options" role="listbox">
                @foreach($categories as $category)
                <li class="cursor-default pl-3 pr-9 text-gray-900 p-1 hover:bg-sky-600 hover:text-white"
                    data-value="{{ $category->id }}">
                    <span class="title">{{ $category->name }}</span>
                </li>
                @end
            </ul>
        </div>
        @else
        <input type="hidden" name="c" value="{{ $cid }}" />
        @end

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