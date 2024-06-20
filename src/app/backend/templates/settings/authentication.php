@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">Authentication</h3>
</div>

<div class="mx-auto flex w-full max-w-7xl items-start gap-x-8 py-10">
    @include categories.php
    <main class="flex-1">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="post" class="form">
                <h2 class="text-base font-semibold text-gray-900">{{ _ Authentication }}</h2>

                <div>
                    {{ html::label("enabled", "Enabled") }}
                    {{ html::checkbox("enabled", $settings->authentication->enabled) }}
                </div>

                <h2 class="text-base font-semibold text-gray-900 mt-5">Facebook</h2>
                <div>
                    {{ html::label("enabled", "Enabled") }}
                    {{ html::checkbox("enabled", $settings->authentication->facebook->enabled) }}
                </div>
                <div>
                    {{ html::label("id" ,"Key") }}
                    {{ html::text("id", $settings->authentication->facebook->id) }}
                </div>
                <div>
                    {{ html::label("secret" ,"Secret") }}
                    {{ html::text("secret", $settings->authentication->facebook->secret) }}
                </div>

                <h2 class="text-base font-semibold text-gray-900 mt-5">Google</h2>
                <div>
                    {{ html::label("enabled", "Enabled") }}
                    {{ html::checkbox("enabled", $settings->authentication->google->enabled) }}
                </div>
                <div>
                    {{ html::label("id" ,"Key") }}
                    {{ html::text("id", $settings->authentication->google->id) }}
                </div>
                <div>
                    {{ html::label("secret" ,"Secret") }}
                    {{ html::text("secret", $settings->authentication->google->secret) }}
                </div>
            </form>
        </div>
    </main>
</div>
