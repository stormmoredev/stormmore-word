<?php

namespace app\frontend\view\forum;

use app\frontend\forum\ForumFinder;
use infrastructure\CategoriesTree;
use infrastructure\routing\Routing;
use IViewComponent;
use Request;
use View;

readonly class ForumCategoryComponent implements IViewComponent
{
    public function __construct(
        private Request $request,
        private ForumFinder  $forumFinder,
        private Routing      $routing)
    { }

    public function view(): View
    {
        $categoryId = $this->request->get('cid');
        $tree = new CategoriesTree($this->forumFinder->listCategories());
        $categories = $tree->toFlat();
        foreach($categories as $category) {
            $category->pl = $category->deep * 5;
            $category->selected = $category->id == $categoryId;
        }
        return view('@frontend-components/forum/categories', [
            'categories' => $categories,
            'routing' => $this->routing
        ]);
    }
}