<?php
/**
 * @var string $uri
 * @var array  $links
 * */
?>

<header class="flex justify-between">
    <a class="w-48" href="{{ url('/') }}">
        <img class="absolute top-0 mt-2 h-16 z-10"  src="{{ url('/public/images/storm-cms.png') }}" />
    </a>
    <nav class="pt-5">
        <ul class="flex rounded-full bg-white/90 px-3 text-sm font-medium text-zinc-800 shadow-lg
                            shadow-zinc-800/5 ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-800/90
                            dark:text-zinc-200 dark:ring-white/10">
            <?php
            $args = ['class' => 'relative block px-3 py-2 transition hover:text-teal-500 dark:hover:text-teal-400'];
            foreach($links as $key => $link) {
                $classes = 'relative block px-3 py-2 transition hover:text-teal-500 dark:hover:text-teal-400';
                if ($link['selected']) {
                    $classes .= ' text-teal-500';
                }
                $links[$key]['attr'] = ['class' => $classes];
            }

            ?>
            <?php foreach($links as $key => $link): ?>
                <li>
                    <?php echo html::link($link['name'], $link['url'], $link['attr']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="pt-5 w-48">
        <div class="hidden flex justify-end show-on-load space-x-5">
            @if($module == 'b' and $settings->blog->enabled)
            <a href="{{ url('/b/add-post') }}" class="block rounded-md bg-sky-600 px-3 py-2 self-center
            text-center text-sm font-semibold text-white shadow-sm hover:bg-sky-500 focus-visible:outline
            focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                {{ _ post_add }}
            </a>
            @end

            <div class="flex justify-end text-sm font-semibold show-on-load">
                @if ($settings->authentication->enabled)
                <a  href=" {{ url('/signin') }} "id="user-anonymous" class="hidden text-sky-600 hover:text-sky-500">
                    {{ _ sign_in }}
                </a>
                @end

                <div id="user-authenticated"></div>
            </div>
        </div>
        <template id="user-authenticated-template">
            <div class="flex group/menu relative">
                <img id="profile-photo" class="hidden h-10 w-10 rounded-md" />
                <div id="profile-initials" class="hidden inline-flex h-10 w-10 items-center justify-center
                rounded-md bg-gray-500 cursor-pointer">
                    <span class="text-xl font-medium leading-none text-white">%username%</span>
                </div>

                <div class="hidden group-hover/menu:block absolute top-10 z-10 right-0 w-48">
                    <div class="mt-2 bg-white origin-top-right rounded-md py-1 shadow-lg ring-1 ring-black ring-opacity-5">
                        <a href="/admin" id="panel" class="block px-4 py-2 text-gray-600 hover:text-sky-700 hidden">
                            {{ _ Panel }}
                        </a>
                        <a href="/profile" class="block px-4 py-2 text-gray-600 hover:text-sky-700">
                            {{ _ Profile }}
                        </a>
                        <a href="/signout" class="block px-4 py-2 text-sm text-gray-600 hover:text-sky-700">
                            {{ _ Sign out }}
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</header>
