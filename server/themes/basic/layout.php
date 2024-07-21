@helpers @frontend/helpers
<html class="h-full antialiased">
<head>
    <title>Stormmore CommunityWord</title>
    <script type="text/javascript" src="{{ url('public/js/stormmore-framework.js') }}"></script>
    <script type="text/javascript" src="{{ url('public/js/shared.js') }}"></script>
    <script type="text/javascript" src="{{ url('public/js/main.js') }}"></script>
    <link rel="icon" type="image/x-icon" href="{{ url('/public/images/storm-cms.ico') }}">
    <link href="{{ url('/public/main.css') }}" rel="stylesheet">
</head>

<body class="bg-zinc-50 dark:bg-black" style="overflow-y: scroll">
    <div class="flex flex-col container mx-auto bg-white max-w-5xl px-10" style="min-height: 100%">
        @component Header
        <main class="flex-1 mt-12">@template</main>
        @static @f-components/footer
    </div>
    @component Settings

    @static @f-components/authentication-modal

    <script type="text/javascript">$.app()</script>
</body>
</html>