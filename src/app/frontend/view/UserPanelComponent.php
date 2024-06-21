<?php

namespace app\frontend\view;

use infrastructure\settings\Settings;
use IViewComponent;
use View;

readonly class UserPanelComponent implements IViewComponent
{
    public function __construct(
        private Settings $settings
    ) { }

    function view(): View
    {
        return view('@frontend-components/user-panel', ['settings' => $this->settings]);
    }
}