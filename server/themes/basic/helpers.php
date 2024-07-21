<?php

use app\shared\presentation\UserProfileDto;

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

function  profile_url(UserProfileDto $profile): void
{
    echo url("/profile/" . $profile->slug);
}

function profile_photo(UserProfileDto $profile, $size = 'sm'): void
{
    $initials = $profile->name[0];
    if ($profile->photo == null) {
        if (str_contains($profile->name, ' ')) {
            $p = explode(' ', $profile->name);
            $initials = $p[0][0] . $p[1][0];
        }
    }

    $size = match ($size) {
        'sm' => 5,
        'lg' => 10
    };

    print_view('@f-components/profile-photo', [
            'initials' => $initials,
            'profile' => $profile->photo,
            'size' => $size
    ]);
}

function print_post($post): void
{
    print_view('@frontend/blog/post-item', ['post' => $post]);
}