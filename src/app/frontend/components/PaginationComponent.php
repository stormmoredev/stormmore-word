<?php

namespace app\frontend\components;

use infrastructure\settings\Settings;
use Request;
use IViewComponent;
use View;



class PaginationComponent implements IViewComponent
{
    public int $count;
    public string $path;

    public function __construct(
        private readonly Request     $request,
        private readonly Settings    $settings)
    { }

    function view(): View
    {
        $path = '/d';
        $page = $this->request->getParameter('page', 1);
        $pageSize = $this->settings->pageSize;

        $pageNum = ceil($this->count / $pageSize);
        $parameters = $this->request->getParameters;
        $parameters['page'] = $page - 1;
        $prevUrl = url($path, $parameters);
        $parameters['page'] = $page + 1;
        $nextUrl = url($path, $parameters);

        return view('@frontend-components/pagination', [
            'nextUrl' => $nextUrl,
            'prevUrl' => $prevUrl,
            'pageNum' => $pageNum,
            'page'  => 7
        ]);
    }
}