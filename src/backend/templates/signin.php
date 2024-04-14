<html xmlns="http://www.w3.org/1999/html">
    <head>
        <title>Blog - login</title>
        <link href="/public/main.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="/public/images/storm-cms.ico">
    </head>

    <body class="d-flex align-items-center py-4 bg-body-tertiary">
        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <a href="/"><img class="mx-auto  w-auto" src="/public/images/storm-cms.png" alt="Storm CMS"></a>
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                    Sign in to your Storm CMS account
                </h2>
            </div>

            <div class="mt-7 sm:mx-auto sm:w-full sm:max-w-sm">
                <form action="/admin/signin" method="POST" class="form">
                    <div class="row">
                        <label for="username">{{ _ Username or email }}</label>
                        <input id="username" name="identity" placeholder="{{ _ Username or email }}" type="text"  required>
                    </div>
                    <div class="row">
                        <label for="password">
                            Password
                        </label>
                        <input id="password" name="password" placeholder="{{ _ Password }}" type="password" required>
                    </div>
                    <div class="mt-2 inline-flex items-center">
                        {{ html::checkbox("remember") }}
                        <label for="remember" class="text-sm ml-2 mt-0 !font-light">
                            {{ _ Remember me }}
                        </label>
                    </div>
                    @if ($message)
                    <div>{{ $message }}</div>
                    @end
                    <div class="mt-5">
                        <button type="submit" class="btn w-full">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>