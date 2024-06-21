<?php
/** @var array $categories */
/** @var \infrastructure\routing\Route $routing */
?>

<div class="w-72">
    <div class="pb-2 pt-4">
        <div class="space-y-2 flex flex-col">
            <?php
                foreach($categories as $category) {
                    $attr = ['class' => 'text-sm text-gray-500'];
                    if ($category->selected) {
                        $attr['class'] .= ' text-sky-500';
                    }
                    $attr['style'] = "padding-left: {$category->pl}px";
                    echo html::link($category->name, $routing->forumCategory($category), $attr);
                }
            ?>
        </div>
    </div>
</div>
