<?php

namespace infrastructure;

class AjaxResult
{
    public int $status;
    public ?object $response;

    public function __construct(int $status, object $response = null)
    {
        $this->status = $status;
        $this->response = $response;
    }
}