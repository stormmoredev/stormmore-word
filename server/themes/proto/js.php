<html class="h-full antialiased">
<head>
    <title>Stormmore CommunityWord</title>
    <link href="{{ url('/public/main.css') }}" rel="stylesheet">
</head>

<body>
    <main x-component="StormmoreCommunityComponent">
        <header>
        </header>
        <div class="mx-auto">
            <div>
                x-bind-up
                <div>Value of property happyNumber comes from HTML, it's initialized from html.</div>
                tag with text and happyNumber binding:
                <span x-bind-up="happyNumber">77</span>
                empty tag with happyNumber binding:
                <span x-bind="happyNumber"></span>
                <div>x-bind-up="happyNumber" populates JS component and other bindings x-bind="happyNumber"</div>
            </div>
            <div class="mt-5">
                <h1>x-disabled</h1>
            </div>
            <div class="mt-5">
                <h2>Click</h2>
                Increment rabbits which comes from html
                <div x-click="incrementRabbits">Increment</div>
                <div x-click="incrementRabbits" x-bind-up="rabbits">99</div>
            </div>
            <div class="mt-5 flex flex-row">
                <div>
                    <div class="h-7 w-7 bg-orange-500" x-if="showOrange"></div>
                    <div class="h-7 w-7 bg-sky-500" x-if-not="showOrange"></div>
                </div>
                <div class="flex">
                    <input type="radio" value="true"  name="div-box" />
                    <input type="radio" value="true"  name="div-box"/>
                </div>

            </div>
            <div class="mt-5">
                <h2>Form</h2>
            </div>
        </div>
    </main>
    <script type="application/javascript">
        $.app()
    </script>
</body>
</html>