<?php

function profile_photo($username, $photo): void
{
    if ($photo != null) {
        ?>
            <img class="h-10 w-10 rounded-md" src="/media/profile/<?php echo $photo ?>" />
        <?php
    } else {
        $initials = $username[0];
        if (str_contains($username, ' ')) {
            $p = explode(' ', $username);
            $initials = $p[0][0] . $p[1][0];
        }
        ?>
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-gray-500">
            <span class="text-xl font-medium leading-none text-white"><?php echo $initials ?></span>
        </div>
        <?php
    }
}

function author_profile($username, $profile): void
{
    $initials = $username[0];
    if ($profile == null) {
        if (str_contains($username, ' ')) {
            $p = explode(' ', $username);
            $initials = $p[0][0] . $p[1][0];
        }
    }

    print_view('@frontend-components/author-profile', [
            'initials' => $initials,
            'profile' => $profile
    ]);
}

function print_post($post): void
{
    print_view('@frontend/blog/post-item', ['post' => $post]);
}