@if ($profile)
<img class="h-10 w-10 rounded-md" src="/media/profile/{{ $profile }}" />
@else
<div class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-gray-500">
    <span class="text-xl font-medium leading-none text-white">{{ $initials }}</span>
</div>
@end
