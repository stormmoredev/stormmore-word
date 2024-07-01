<?php

namespace app\frontend\components;

use infrastructure\settings\Settings;
use IViewComponent;
use View;
use Request;

class HeaderComponent implements IViewComponent
{
    private array $links;

    public function __construct(
        private readonly Settings $settings,
        private readonly Request $request
    )
    {
        $this->links = [
            'b' => ['name' => _('Blog'), 'url' => "/b", 'urls' => ['b'], 'selected' => false],
            'c' => ['name' => _('Community'), 'url' => "/c", 'urls' => ['c'], 'selected' => false],
            'f' => ['name' => _('Forum'), 'url' => "/f", 'urls' => ['f', 'fc'], 'selected' => false]
        ];
    }

    function view(): View
    {
        $uri = $this->request->requestUri;
        $module = $this->selected($uri);
        $this->links[$module]['selected'] = true;

        return view('@frontend-components/header', [
            'links' => $this->links,
            'settings' => $this->settings,
            'module' => $module
        ]);
    }

    private function selected($uri): string
    {
        foreach($this->links as $key => $link) {
            foreach ($link['urls'] as $url) {
                if ($uri == "/$url" or str_starts_with($uri, "/$url?") or str_starts_with($uri, "/$url/")) {
                    return $key;
                }
            }
        }

        return $this->settings->homepage;
    }
}