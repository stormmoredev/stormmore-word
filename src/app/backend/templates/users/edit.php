@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">Edit user</h3>
</div>

<form method="POST" class="form mt-10">
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

            {{ $form->label("name", "Username") }}
            {{ $form->text("name", disabled: true) }}
            {{ $form->error("name") }}

            {{ $form->label("first_name", "First name") }}
            {{ $form->text("first_name") }}
            {{ $form->error("first_name") }}

            {{ $form->label("last_name", "Last name") }}
            {{ $form->text("last_name") }}
            {{ $form->error("last_name") }}

            {{ $form->label("email", "Email") }}
            {{ $form->text("email", disabled: true) }}
            {{ $form->error("email") }}

            @include role.php

            <div class="mt-4 mb-2 flex justify-end">
                <a href="{{ url('/admin/users/resend-confirmation-email', ['user-id' => $uid]) }}"
                   class="text-sm text-blue-500 leading-6">
                    {{ _ Resend confirmation email }}
                </a>
            </div>

            <button type="submit" class="btn w-full">Save</button>
    </div>
</form>
