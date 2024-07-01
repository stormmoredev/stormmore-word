<?php /** @var object $post */ ?>

<article class="flex flex-row">
    <div class="flex flex-col">
        {{ author_profile($post->username, $post->profile) }}
        <a href="{{ url('/u/profile') }}" class="text-sm font-light pt-2">{{ $post->username }}</a>
    </div>
    <div class="pl-24 flex-1">
        <a class="text-base font-semibold tracking-tight text-zinc-800 dark:text-zinc-100"
           href="<?php echo url("/b/$post->slug") ?>">
            {{ $post->title }}
        </a>
        @if ($post->url)
        <iframe class="py-4" height="240" width="384" src="{{ $post->url }}"></iframe>
        @end
        @if ($post->subtitle)
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            <a href="<?php echo url("/b/$post->slug") ?>">{{ $post->subtitle }}</a>
        </p>
        @end
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
    </div>
</article>