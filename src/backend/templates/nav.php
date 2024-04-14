<?php
    use infrastructure\settings\Settings;
    use authentication\StormUser;

    $user = di(IdentityUser::class);
    $request = di(Request::class);
    $settings = di(Settings::class);
    $user = di(StormUser::class);
    $selectedLanguage = $user->language->primary;
    $languages = [] ;
    foreach($settings->enabledLanguages as $lang) {
        $languages[$lang->primary] = $lang->primary;
    }

    $uri = $request->uri;
    $uri = $uri == '/admin' ? '/admin/articles' : $uri;
    $items = ["/admin/articles" =>  _("Articles"),
        "/admin/users" => _("Users"),
        "/admin/sessions" => _("Sessions"),
        "/admin/settings" => _("Settings")]

?>

<nav class="bg-white border-b h-16 text-sm font-medium text-slate-500">
    <div class="container flex justify-between mx-auto h-16 items-center relative">
        <a href="/">
            <img class="absolute top-0 mt-2 h-16 z-10" src="/public/images/storm-cms.png" />
        </a>
        <div class="ml-[90px] text-slate-500 flex h-full box-border">
            <a href="/" class="mr-7 text-slate-400 flex items-center">Storm CMS</a>
            <!--<div class="flex items-center pt-[1px] border-b border-b-blue-400 mr-3 text-slate-800 relative">
                <a href="" class="px-1 border-b-blue-300">Content</a>
                <div class="absolute left-0 w-full top-16 bg-white ml-1">
                    <a href="">Entries</a>
                    <a href="">Replies</a>
                    <a href="">Aggregates</a>
                </div>
            </div>-->
            @foreach($items as $itemUrl => $itemName)
                @if (str_starts_with($uri, $itemUrl))
                    <div class="flex items-center pt-[1px] border-b border-b-blue-400 mr-3 text-slate-800">
                        <a href="{{ $itemUrl }}" class="px-1 border-b-blue-300">{{ $itemName }}</a>
                    </div>
                @else
                    <div class="hover:text-slate-600 hover:pt-[1px] hover:border-b border-b-blue-300
                                                flex items-center mr-3">
                        <a href="{{ $itemUrl }}" class="px-1">{{ $itemName }}</a>
                    </div>
                @end
            @end

            @if ($settings->multiLanguage)
            <div class="flex items-center">
                <form id="change-language" action="/language">
                    {{ html::select('user-lang', $languages, selected: $selectedLanguage, onChange: "submitLangauge()") }}
                </form>
            </div>
            <script type="text/javascript">
                function submitLangauge() {
                    document.getElementById('change-language').submit();
                }
            </script>
            @end
        </div>
        <div class="grow flex justify-end mr-5">
            <span class="rounded-l-md border-l border-t border-b pt-[2px] pl-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </span>
            <input type="text" class="border-r border-t border-b rounded-r-md p-1 pl-2" placeholder="{{ _ Search }}" />
        </div>
        <div class="hover:text-slate-800 hover:pt-[1px] h-full
                                hover:border-b border-b-red-500
                                flex items-center">
            <a href="/signout">
                {{ _ Sign out }}
            </a>
        </div>
    </div>
</nav>