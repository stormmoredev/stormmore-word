@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">{{ _ Cache }}</h3>
</div>

<div class="mx-auto flex w-full max-w-7xl items-start gap-x-8 py-10">
    @include categories.php
    <main class="flex-1">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="post" class="form">
                <div>{{ _ cached_view_files }} </div>
                <div>{{ _ cached_file_number }} {{ $info->viewFilesNum }}</div>
                <div>{{ _ cached_file_size }} {{ $info->viewFilesSize }}MB</div>

                <div>{{ _ cached_response_files }} </div>
                <div>{{ _ cached_file_number }} {{ $info->responseFilesNum }}</div>
                <div>{{ _ cached_file_size }} {{ $info->responseFilesSize }}MB</div>

                <button type="submit" class="btn w-full mt-7">{{ _ Remove all }}</button>
            </form>
        </div>
    </main>
</div>
