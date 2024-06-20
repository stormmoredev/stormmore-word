@layout @backend-layout

<form action="{{ url('/admin/forum/categories/add') }}" method="post">
    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="col-span-full">
            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                {{ _ Name }}
            </label>
            <div class="mt-2">
                <input type="text" name="name" id="name" maxlength="256"
                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                                ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                                focus:ring-sky-600 sm:text-sm sm:leading-6">
            </div>
            {{ $form->error("name") }}
        </div>
        <div class="col-span-full">
            <label for="sequence" class="block text-sm font-medium leading-6 text-gray-900">
                {{ _ Order }}
            </label>
            <input type="text" name="sequence" value="1"
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset
                                ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                                focus:ring-sky-600 sm:text-sm sm:leading-6" />
            {{ $form->error("sequence") }}
        </div>
        <div class="col-span-full">
            <label for="parent_id" class="block text-sm font-medium leading-6 text-gray-900">
                {{ _ Parent category }}
            </label>
            {{ html::select('parent_id', $categories->toOptionList()) }}
        </div>
        <div class="col-span-full">
            <label for="description" class="block text-sm font-medium leading-6 text-gray-900">
                {{ _ Description }}
            </label>
            <div class="mt-2">
                        <textarea id="description" name="description" rows="3" maxlength="1024"
                                  class="block w-full rounded-md
                                    border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300
                                    placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-600
                                    sm:text-sm sm:leading-6"></textarea>
                        {{ $form->error("description") }}
            </div>
        </div>
    </div>
    <div class="mt-6 flex justify-end">
        <button type="submit" class="block rounded-md bg-sky-600 px-3 py-2
                text-center text-sm font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline
                focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
            {{ _ Add }}
        </button>
    </div>
</form>