<?php

namespace infrastructure;

class PageCriteria
{
    private int $page;
    private int $pageSize;

    public function setPage($page): void
    {
        if (!$page || !is_numeric($page)) $this->page = 1;
        if ($page < 1) $this->page = 1;

        $this->page = $page;
    }

    public function setPageSize($pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }

    public function getLimit(): int
    {
        return $this->pageSize;
    }
}