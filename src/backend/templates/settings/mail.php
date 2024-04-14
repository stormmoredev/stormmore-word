@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">Settings</h3>
</div>

<div class="mx-auto flex w-full max-w-7xl items-start gap-x-8 py-10">
    @include categories.php
    <main class="flex-1">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="post" class="form">
                <h2 class="text-base font-semibold text-gray-900">From</h2>
                <div>
                    {{ html::label("name" ,"Name") }}
                    {{ html::text("name", $settings->mail->from->name) }}
                </div>
                <div>
                    {{ html::label("address" ,"Address") }}
                    {{ html::text("address", $settings->mail->from->address) }}
                </div>
                <div class="mb-2 flex flex-row justify-between text-base font-semibold">
                    <h2 class="text-gray-900">SMTP server</h2>
                    <a href="/admin/settings/mail/smtp-test"
                       class="text-sky-600 hover:text-sky-500">
                        Test SMTP connection
                    </a>
                </div>

                <div>
                    {{ html::label("host" ,"Host") }}
                    {{ html::text("host", $settings->mail->host) }}
                </div>
                <div>
                    {{ html::label("isAuthenticationEnabled", "Enable authentication") }}
                    {{ html::checkbox("isAuthenticationEnabled", $settings->mail->isAuthenticationEnabled) }}
                </div>
                <div>
                    {{ html::label("username" ,"Username") }}
                    {{ html::text("username", $settings->mail->username) }}
                </div>
                <div>
                    {{ html::label("password" ,"Password") }}
                    {{ html::password("password", $settings->mail->password) }}
                </div>
                <div>
                    {{ html::label("isTlsEnabled", "Enable TLS encryption") }}
                    {{ html::checkbox("isTlsEnabled", $settings->mail->isTlsEnabled) }}
                </div>
                <div>
                    {{ html::label("port" ,"Port") }}
                    {{ html::text("port", $settings->mail->port) }}
                </div>
                <button type="submit" class="btn w-full mt-7">{{ _ Save }}</button>
            </form>
        </div>
    </main>
</div>

