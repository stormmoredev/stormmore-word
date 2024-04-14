@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">Add user</h3>
</div>

<div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form action="/admin/users/add" method="POST" class="form">
        {{ $form->label("name", "Username") }}
        {{ $form->text("name") }}
        {{ $form->error("name") }}

        {{ $form->label("first_name", "First name") }}
        {{ $form->text("first_name") }}
        {{ $form->error("first_name") }}

        {{ $form->label("last_name", "Last name") }}
        {{ $form->text("last_name") }}
        {{ $form->error("last_name") }}

        {{ $form->label("email", "Email") }}
        {{ $form->text("email") }}
        {{ $form->error("email") }}

        {{ $form->label("password", "Password") }}
        {{ $form->password("password") }}
        {{ $form->error("password") }}

        {{ $form->label("password2", "Password") }}
        {{ $form->password("password2") }}
        {{ $form->error("password2") }}

        @include role.php

        <button type="submit" class="btn w-full">Save</button>
    </form>
</div>
