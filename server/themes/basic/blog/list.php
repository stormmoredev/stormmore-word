<?php /** @var array $posts */ ?>

@layout @frontend/layout.php

<div class="md:border-l md:border-gray-100 pl-6 mt-14">
    <div class="flex flex-col space-y-16">
        <?php foreach($posts as $post): ?>
        <?php print_post($post) ?>
        <?php endforeach; ?>
    </div>
</div>