<?php use app\shared\presentation\ReplyDto; ?>
<?php use infrastructure\settings\Settings; ?>

<?php /** @var string $slug */ ?>
<?php /** @var object $post */ ?>
<?php /** @var ReplyDto[] $replies */ ?>
<?php /** @var Settings $settings */ ?>

@layout @frontend/layout.php

<article id="article" x-component="PostComponent">
    <input type="hidden" x-bind-up="id" value="<?php echo $post->id ?>" />
    <header class="mb-14">
        <a id="back" href="/" title="<?php echo _('back_to_articles') ?>">
            @static @f-icons/back-arrow.svg
        </a>

        <div class="my-10 flex leading-none">
            <a href="<?php profile_url($post->author) ?>"><?php profile_photo($post->author, 'lg'); ?></a>
            <div class="ml-7 content-between">
                <div>
                    <a href="<?php profile_url($post->author); ?>"><?php echo $post->author->name ?></a>
                    <a href="<?php  ?>" x-click="follow" class="text-sky-600 hover:text-sky-700">
                        <?php echo _('follow_user') ?>
                    </a>
                </div>
                <div class="text-sm font-light text-zinc-600 pt-1">
                    <time class="convert-to-datetime-diff"
                          title="<?php echo _('published_at') ?>"
                          data-date="<?php echo $post->published_at  ?>"></time>
                    <span><?php echo $post->author->followers_num ?> followers</span>
                    <span><?php echo $post->author->entries_num ?> articles</span>
                    <span></span>
                </div>
            </div>
        </div>

        <h1>{{ $post->title }}</h1>
        <div class="subtitle">{{ $post->subtitle }}</div>

        <div class="flex justify-between w-full mt-7 font-light text-zinc-400">
            <div class="flex text-base items-center">
                <div
                   title="<?php echo _('post_likes') ?>"
                   x-click="like"
                   class="flex items-center hover:text-zinc-600">
                    <div class="size-5 mr-1">
                        @static @f-icons/heart.svg
                    </div>
                    <span x-bind-up="likes"><?php echo $post->votes_num ?></span>
                </div>
                <a href="" title="<?php echo _('post_replies') ?>"
                   class="flex items-center ml-5"
                   x-click="comment">
                    <div class="size-5 mr-1">
                        @static @f-icons/chat-bubble.svg
                    </div>
                    <?php echo $post->replies_num ?>
                </a>
        </div>
    </header>

    <div class="content">{{ $post->content }}</div>

    <?php if ($settings->blog->comments->enabled): ?>
    <div class="mt-12">
        @if (RedirectMessage::isset('comment-success'))
        <div class="mb-7 flex flex-row w-full px-4 py-4 text-base shadow-md
             text-gray-800 rounded-lg font-regular bg-gray-900/10">
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-green-800">
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25
                        17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06
                        1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"></path>
            </svg>
            <span class="ml-5">{{ _ Your comment has been added. }}</span>
        </div>
        @end
        <div x-if-not="isAuthenticated" class="flex justify-center w-full">
            <a href="<?php echo url('/signin') ?>"
               x-click="comment"
               class="font-semibold leading-6 text-sky-600 hover:text-sky-500">
                <?php echo _('post_write_comment') ?>
            </a>
        </div>
        <div x-if="isAuthenticated">
            <form action="<?php echo url("/b/add-reply/$slug") ?>" method="post" class="relative">
                <input name="post-id" type="hidden" value="<?php echo $post->id ?>"/>
                <div class="overflow-hidden rounded-lg border border-gray-300 shadow-sm">
                <textarea name="content" class="block w-full resize-none border-0 p-3
                    h-36 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                          placeholder="<?php echo _('post_write_comment') ?>"></textarea>
                    <div aria-hidden="true">
                        <div class="h-px"></div>
                        <div class="py-2">
                            <div class="py-px">
                                <div class="h-9"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="absolute inset-x-px bottom-0">
                    <div class="flex items-center justify-between space-x-3 border-t border-gray-200 px-2 py-2 sm:px-3">
                        <div class="flex"></div>
                        <div class="flex-shrink-0">
                            <button type="submit" class="btn gray">{{ _ Save }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (count($replies)): ?>
    <div class="mt-10">
        <?php foreach($replies as $reply): ?>
        <div id="comment-<?php echo $reply->id ?>" class="flex mt-5 space-x-4 text-sm">
            <div class="flex-none py-0">
                <a href="<?php profile_url($reply->author); ?>">
                    <?php profile_photo($reply->author, 'lg') ?>
                </a>
            </div>
            <div class="flex-1 py-0 ">
                <a href="<?php profile_url($reply->author); ?>" class="font-medium text-gray-900">
                    <?php echo $reply->author->name ?>
                </a>
                <p class="text-xs convert-to-datetime-diff"
                   data-date="{{ $post->created_at | js_datetime }}"
                </p>
                <div class="text-base mt-2 max-w-none text-gray-700">
                    <p><?php echo $reply->content ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</article>

