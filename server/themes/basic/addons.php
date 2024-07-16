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

function post_titled_media_thumb(?string $titled_media): void
{
    if (empty($titled_media)) return;

    if (str_starts_with($titled_media, 'https://www.youtube.com/')) {
        echo "<iframe height=\"128\" width=\"256\" src=\"$titled_media\"></iframe>";
    }
    else {
        $src = url(concatenate_paths('media', $titled_media));
        echo "<img src=\"$src\" />";
    }
}

function author_profile_sm($username, $profile): void
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
    print_view('@frontend/blog/list-item', ['post' => $post]);
}