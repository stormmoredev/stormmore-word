<?php /** @var array $posts */ ?>

@layout @frontend/layout.php

<div class="md:border-l md:border-gray-100 md:pl-6 md:dark:border-zinc-700/40 ">
    <div class="flex flex-col space-y-16">
        <?php foreach($posts as $post): ?>
        <?php print_post($post) ?>
        <?php endforeach; ?>
    </div>
</div>