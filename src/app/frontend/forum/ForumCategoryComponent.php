<?php

namespace app\frontend\forum;

use IViewComponent;
use Request;
use View;

class ForumCategoryComponent implements IViewComponent
{
    public function __construct(Request $request)
    {
    }

    function print(): View
    {
        $name = 'Michał';
        $view = view('@frontend/forum/categories', ['name' => $name]);
        return $view;
    }
}