@layout @backend-layout

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ _ Threads }}</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all  threads in descending order.</p>
        </div>
    </div>
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Title</th>
                        <th class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 sm:pl-0">Name</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Updated at</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">Created at</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($threads as $thread)
                    <tr>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $thread->author_name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ $thread->title }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $thread->updated_at }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $thread->created_at }}</td>
                    </tr>
                    @end
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>