<?php /** @var object $post */ ?>

<article>
    <div class="flex flex-row justify-between">
        <div>
            <div class="flex flex-row text-sm font-light pt-2">
                <a href="<?php echo url('/u/profile') ?>"><?php author_profile_sm($post->username, $post->profile) ?></a>
                <a href="<?php echo url('/u/profile') ?>" class="pl-2 leading-none"><?php echo $post->username ?></a>
            </div>
            <div>
                <a class="text-base font-semibold tracking-tight text-zinc-800 dark:text-zinc-100 pt-3"
                   href="<?php echo url("/b/$post->slug") ?>">
                    <?php echo $post->title ?>
                </a>
            </div>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                <a href="<?php echo url("/b/$post->slug") ?>"><?php echo $post->subtitle ?></a>
            </p>
        </div>
        <div><?php post_titled_media_thumb($post->titled_media); ?></div>
    </div>
    <div class="text-sm font-light text-zinc-500 pt-4 space-x-5">
        <a href="<?php echo url("/b/$post->slug") ?>"
           class="w-24 convert-to-datetime-diff" data-date="{{ $post->created_at | js_datetime }}"></a>
        <a href="<?php echo url("/b/$post->slug") ?>">
            {{ $post->votes_num }} {{ _ post_gratitude }}
        </a>
        <a href="<?php echo url("/b/$post->slug") ?>">
            {{ $post->replies_num }} {{ _ post_comments }}
        </a>
    </div>
</article>