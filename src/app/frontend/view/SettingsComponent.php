<?php

namespace app\frontend\view;

use infrastructure\settings\Settings;
use IViewComponent;
use View;

readonly class SettingsComponent implements IViewComponent
{
    public function __construct(
        private Settings $settings
    ) { }

    function view(): View
    {
        return view('@frontend-components/settings', ['settings' => $this->settings]);
    }
}