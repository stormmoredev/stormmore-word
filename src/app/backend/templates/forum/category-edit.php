@layout @backend-layout

<div class="flex">
    <a href=" {{ url('/admin/forum/categories') }}" type="button"
       class="flex mt-1 mr-5 h-5 w-5 items-center justify-center rounded-full bg-white
                shadow-md shadow-zinc-800/5 ring-1 ring-zinc-900/5 hover:bg-gray-50">
        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true" class="h-4 w-4 stroke-zinc-500">
            <path d="M7.25 11.25 3.75 8m0 0 3.5-3.25M3.75 8h8.5" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round">
            </path>
        </svg>
    </a>
    <div>
        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ _ Category edition  }}</h1>
        <p class="mt-2 text-sm text-gray-700">{{ $category->name }}</p>
    </div>

</div>

<div class="w-full mt-5">
    <form action="{{ url('/admin/forum/categories/edit', ['id' => $category->id]) }}" method="post">
        <div class="grid gap-y-8">
            <div>
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ _ Name }}
                </label>
                <div class="mt-2">
                    <input type="text" name="name" id="name" maxlength="256"
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                            ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                            focus:ring-sky-600 sm:text-sm sm:leading-6" value="{{ $category->name }}">
                </div>
                {{ $form->error("name") }}
            </div>
            <div class="col-span-full">
                <label for="sequence" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ _ Sequence }}
                </label>
                <input type="text" name="sequence" value="{{ $category->sequence }}"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                                ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                                focus:ring-sky-600 sm:text-sm sm:leading-6" />
                {{ $form->error("sequence") }}
            </div>
            <div>
                <label for="parent_id" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ _ Parent category }}
                </label>
                {{ html::select('parent_id', $categories->toOptionList(), $category->parent_id) }}
            </div>
            <div>
                <label for="description" class="block text-sm font-medium leading-6 text-gray-900">
                    {{ _ Description }}
                </label>
                <div class="mt-2">
                    <textarea id="description" name="description" rows="3" maxlength="1024"
                              class="block w-full rounded-md
                                border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300
                                placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-600
                                sm:text-sm sm:leading-6">{{ $category->description }}</textarea>
                    {{ $form->error('description') }}
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-between">
            <a href="{{ url('/admin/forum/categories/delete', ['id' => $category->id]) }}"
               class="block rounded-md bg-red-500 px-3 py-2
                text-center text-sm font-semibold text-white shadow-sm hover:bg-red-400 focus-visible:outline
                focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                {{ _ Delete }}
            </a>
            <button type="submit" class="block rounded-md bg-sky-600 px-3 py-2
            text-center text-sm font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline
            focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                {{ _ Save }}
            </button>
        </div>
    </form>
</div>


