<?php

$request = di(Request::class);
$settings = di(Settings::class);
$page = $request->getParameter('page', 1);
$pageSize = $settings->pageSize;

$pageNum = ceil($count / $pageSize);
$parameters = $request->getParameters;
$parameters['page'] = $page - 1;
$prevUrl = url($request->uri, $parameters);
$parameters['page'] = $page + 1;
$nextUrl = url($request->uri, $parameters);
?>

<div class="flex justify-between my-5">
    <div>{{ _ Showing %s of %s pages | $page $pageNum }}</div>
    <div class="flex justify-between">
        @if ($page > 1)
        <a href="{{ $prevUrl }}" class="btn">{{ _ Previous }}</a>
        @else
        <button class="btn" disabled>{{ _ Previous }}</button>
        @end

        @if ($page < $pageNum)
        <a href="{{ $nextUrl }}" class="ml-2 btn">{{ _ Next }}</a>
        @else
        <button class="ml-2 btn" disabled>{{ _ Previous }}</button>
        @end
    </div>
</div>

