@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">{{ _ Languages }}</h3>
</div>

<div class="mx-auto flex w-full max-w-7xl items-start gap-x-8 py-10">
    @include categories.php
    <main class="flex-1">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="post" class="form">
                {{ html::label("multiLanguage", "Multi language") }}
                {{ html::checkbox("multiLanguage", checked: $multiLanguage) }}

                {{ html::label("defaultLanguage" ,"Default language") }}
                {{ html::select("defaultLanguage", values: $enabled, selected: $default->primary ) }}

                {{ html::label("languages", "Languages") }}
                <div class="overflow-auto h-72">
                    @foreach($list as $id => $name)
                    <div>{{ html::checkbox("enabled[$id]", in_array($id, $enabledCodes)) }} {{ $name }}</div>
                    @end
                </div>
                <button type="submit" class="btn w-full mt-7">{{ _ Save }}</button>
            </form>
        </div>
    </main>
</div>