@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">Settings</h3>
</div>

<div class="mx-auto flex w-full max-w-7xl items-start gap-x-8 py-10">
    @include categories.php
    <main class="flex-1">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="post" class="form">
                {{ html::label("defaultRole" ,"Default role") }}
                {{ html::select("defaultRole", values: $roles, selected: $settings->defaultRole ) }}

                {{ html::label("name" ,"Name") }}
                {{ html::text("name", $settings->name) }}

                {{ html::label("pageSize", "Page Size") }}
                {{ html::text("pageSize", value: $settings->pageSize) }}

                <button type="submit" class="btn w-full mt-7">Save</button>
            </form>
        </div>
    </main>
</div>

