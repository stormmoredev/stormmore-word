<?php

namespace app\frontend\forum;

use IViewComponent;
use Request;

class ForumCategoryComponent implements IViewComponent
{
    public function __construct(Request $request)
    {

    }
    function print(): void
    {
        echo "ellloooo component";
    }
}