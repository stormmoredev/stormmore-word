@layout @proto/layout.php

<?php /** @var array $articles */ ?>

<div class="mt-24 w-full space-y-24">
    <?php foreach($articles as $article): ?>
        <article class="flex flex-row group">
            <img class="rounded-md h-16 w-16 flex-none" src="<?php echo $article->author->profile ?>">
            <div class="ml-5 grow">
                <div class="pr-20">
                    <a href="" class="mb-4 block-inline font-bold leading-none hover:underline">
                        <?php echo $article->title ?>
                    </a>
                    <div class="flex flex-col mb-3">
                        <div class="flex leading-none text-sm mt-4">
                            written by
                            <a href="" class="px-1 hover:underline tooltip">
                                @<?php echo $article->author->name ?>
                                <div class="tooltiptext">If you like article feel free to say thanks and donate Brain</div>
                            </a>
                            for you and published 13-07-2022
                        </div>
                    </div>

                    <div class="block mt-2">
                        <a href=""><?php echo $article->subtitle ?></a>
                    </div>
                </div>
                <div class="mt-5 flex justify-between">
                    <div>77 enters 14 comments</div>
                    <div>fav bookmark ...</div>
                </div>
            </div>
            <div class="flex-none">
                <img src="<?php echo $article->media ?>"
                     class="w-44 h-24 group-hover:grayscale-0 object-cover grayscal rounded-md" />
            </div>
        </article>
    <?php endforeach; ?>
</div>
