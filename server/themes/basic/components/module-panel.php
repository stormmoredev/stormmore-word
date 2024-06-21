<?php
/** @var string $requestUri */
?>
<nav class="pointer-events-auto hidden md:block pt-5">
    <ul class="flex rounded-full bg-white/90 px-3 text-sm font-medium text-zinc-800 shadow-lg
                            shadow-zinc-800/5 ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-800/90
                            dark:text-zinc-200 dark:ring-white/10">
        <?php
            $args = ['class' => 'relative block px-3 py-2 transition hover:text-teal-500 dark:hover:text-teal-400'];
            $links = [
                [_('Blog'), "/", $args],
                [_('Community'), "/c", $args, ],
                [_('Forum'), "/f", $args]
            ];
            if ($requestUri == '/f' or str_starts_with($requestUri, '/f?') or str_starts_with($requestUri, '/f/')) {
                $links[2][2]['class'] .= ' text-teal-500';
            }
            else if ($requestUri == '/c' or str_starts_with($requestUri, '/c?') or str_starts_with($requestUri, '/c/')) {
                $links[2][2]['class'] .= ' text-teal-500';
            }
            else {
                $links[0][2]['class'] .= ' text-teal-500';
            }
            ?>
        <?php foreach($links as $link): ?>
            <li>
                <?php echo html::link($link[0], $link[1], $link[2]) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>