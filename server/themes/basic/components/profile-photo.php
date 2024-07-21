<?php /** @var  $size */ ?>
<?php
$class = "h-$size w-$size"
?>
@if ($profile)
<img class="<?php echo $class ?> rounded-md" src="/media/profile/{{ $profile }}" />
@else
<div class="<?php echo $class ?> inline-flex items-center justify-center rounded-md bg-gray-500">
    <span class="text-sm font-medium leading-none text-white">{{ $initials }}</span>
</div>
@end
