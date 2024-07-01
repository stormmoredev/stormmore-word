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