<?php

use infrastructure\settings\Settings;

function pagination($path, $count): void
{
    $request = di(Request::class);
    $settings = di(Settings::class);
    $page = $request->getParameter('page', 1);
    $pageSize = $settings->pageSize;

    $pageNum = ceil($count / $pageSize);
    $parameters = $request->getParameters;
    $parameters['page'] = $page - 1;
    $prevUrl = url($path, $parameters);
    $parameters['page'] = $page + 1;
    $nextUrl = url($path, $parameters);
    ?>
    <div class="flex justify-between my-5">
        <div><?php echo _('Showing %s of %s pages', $page, $pageNum) ?></div>
        <div class="flex justify-between">
            <?php if ($page > 1): ?>
            <a href="<?php echo $prevUrl ?>" class="btn"><?php echo _('Previous') ?></a>
            <?php else: ?>
            <button class="btn" disabled><?php echo _('Previous') ?></button>
            <?php endif ?>

            <?php if ($page < $pageNum): ?>
            <a href="<?php echo $nextUrl ?>" class="ml-2 btn"><?php echo  _('Next') ?></a>
            <?php else: ?>
            <button class="ml-2 btn" disabled><?php echo _('Previous') ?></button>
            <?php endif ?>
        </div>
    </div>
    <?php
}


?>

