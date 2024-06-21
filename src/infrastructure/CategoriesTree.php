<?php

namespace infrastructure;

class CategoriesTree
{
    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function toFlat():array
    {
        return $this->buildFlatTree(null, 0);
    }

    private function buildFlatTree(?int $parentId, int $deep): array
    {
        $categories = array();
        foreach ($this->items as $item)
        {
            if ($item->parent_id == $parentId) {
                $item->deep = $deep;
                $item->nameWithNestPrefix = str_repeat('-', $item->deep) . $item->name;
                $categories[] = $item;
                $categories = array_merge($categories, $this->buildFlatTree($item->id, $deep + 1));
            }
        }
        return $categories;
    }
}