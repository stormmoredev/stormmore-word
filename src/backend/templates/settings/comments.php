@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">{{ _ Comments }}</h3>
</div>

<div class="mx-auto flex w-full max-w-7xl items-start gap-x-8 py-10">
    @include categories.php
    <main class="flex-1">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="post" class="form">
                <div>
                    {{ html::label("enabled", "Enable") }}
                    {{ html::checkbox("enabled", $settings->comments->enabled) }}
                </div>
                <button type="submit" class="btn w-full mt-7">{{ _ Save }}</button>
            </form>
        </div>
    </main>
</div>

