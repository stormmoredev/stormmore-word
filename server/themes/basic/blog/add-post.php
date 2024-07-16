<?php

use app\frontend\blog\AddPostForm;

/** @var AddPostForm $form */

?>

@layout @frontend/layout.php

<form id="add-post-form" action="{{ url('/b/add-post') }}" method="post">
    <div class="px-3 pt-3">
        <div>
            <?php $form->printError('title') ?>
            <input type="text" name="title" id="title"
                   maxlength="128"
                   placeholder="<?php echo _('post_title') ?>"
                   value="<?php echo $form->getValue('title') ?>">
        </div>
        <div class="pt-3">
            <?php $form->printError('subtitle') ?>
            <input type="text" name="subtitle" id="subtitle"
                   maxlength="256"
                   value="<?php echo $form->getValue('subtitle') ?>"
                   placeholder="<?php echo _('post_subtitle'); ?>">
        </div>
        <div class="pt-3">
            <?php $form->printError('media') ?>
            <input type="text" name="media" id="media"
                   maxlength="2048"
                   placeholder="<?php echo _('post_media_media'); ?>"
                   value="<?php echo $form->getValue('media') ?>"
                   autocomplete="off">
        </div>
        <div class="pt-4">
            <?php $form->printError('content') ?>
             <textarea type="text" rows="1" name="content" id="content"
                       placeholder="<?php echo _('post_placeholder') ?>"
             ><?php echo $form->getValue('content') ?></textarea>
        </div>
    </div>
    <div class="flex justify-end px-2 py-2 border-t border-gray-300">
        <button type="submit" class="btn blue">{{ _ post_save }}</button>
    </div>
</form>