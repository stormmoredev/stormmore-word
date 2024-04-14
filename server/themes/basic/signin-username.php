<?php
use infrastructure\settings\Settings;
$settings = di(Settings::class);
?>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>{{ $settings->name }}</title>
    <link href="/public/main.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/public/images/storm-cms.ico">
</head>

<body class="d-flex align-items-center py-2 bg-body-tertiary">
    <div class="flex min-h-full flex-col justify-center px-6 py-7 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <a href="/"><img class="mx-auto  w-auto" src="/public/images/storm-cms.png" alt="Storm CMS"></a>
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                {{ _ Username is taken }} <br> {{ _ Please choose another }}
            </h2>
        </div>

        <div class="mt-7 sm:mx-auto sm:w-full sm:max-w-sm">
            <form method="GET" class="form">
                <div class="row">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text"
                           placeholder="{{ $username }}" required>
                </div>
                <button type="submit" class="mt-5 btn w-full">{{ _ Save }}</button>
            </form>
        </div>
    </div>
</body>
</html>