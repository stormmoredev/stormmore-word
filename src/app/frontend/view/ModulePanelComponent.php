<?php

namespace app\frontend\view;

use infrastructure\settings\Settings;
use IViewComponent;
use View;
use Request;

readonly class ModulePanelComponent implements IViewComponent
{
    public function __construct(
        private Settings $settings,
        private Request $request
    ) { }

    function view(): View
    {
        return view('@frontend-components/module-panel', [
            'settings' => $this->settings,
            'requestUri' => $this->request->requestUri
        ]);
    }
}