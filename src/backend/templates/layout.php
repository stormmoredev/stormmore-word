<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Storm CMS</title>
    <link rel="icon" type="image/x-icon" href="/public/storm-cms.ico">

    <script src="/public/editor/medium-editor.min.js"></script>
    <script src="/public/editor/medium-editor-multi-placeholder.min.js"></script>
    <link rel="stylesheet" href="/public/editor/medium-editor.min.css" />
    <link rel="stylesheet" href="/public/editor/medium-editor.theme.css" />

    <link href="/public/main.css" rel="stylesheet">
</head>
<body class="leading-none">
    <main class="flex flex-col w-full h-full">
        @include nav.php
        <div class="container mx-auto pt-5 pl-2 mt-7">
            @include notifications.php
            @template
        </div>
    </main>
</body>
</html>