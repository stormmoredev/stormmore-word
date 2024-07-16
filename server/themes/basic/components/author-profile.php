@if ($profile)
<img class="h-5 w-5 rounded-md" src="/media/profile/{{ $profile }}" />
@else
<div class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-gray-500">
    <span class="text-sm font-medium leading-none text-white">{{ $initials }}</span>
</div>
@end
