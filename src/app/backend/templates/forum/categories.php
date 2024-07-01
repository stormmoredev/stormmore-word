@layout @backend-layout

<div class="flex justify-between">
    <div>
        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ _ Categories }}</h1>
        <p class="mt-2 text-sm text-gray-700">A list of all categories in descending order.</p>
    </div>
    <div class="align-center self-center">
        <a class="btn" href="/admin/forum/categories/add">{{ _ Add }}</a>
    </div>
</div>

<div class="flex flex-col w-full mt-5">
    @foreach($categories as $category)
    <div class="mt-2 text-base leading-7 text-gray-600">
        <a href="{{ url('/admin/forum/categories/edit', ['id' => $category->id]) }}"
            title="{{ _ Edit category '%s' | $category->name }}">
            {{ $category->nameWithNestPrefix }}
        </a>
    </div>
    @end
</div>


