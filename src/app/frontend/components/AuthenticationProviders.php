<?php

namespace app\frontend\components;

use infrastructure\settings\Settings;
use IViewComponent;
use View;
use Request;

readonly class AuthenticationProvidersComponent implements IViewComponent
{
    public function __construct(
        private Settings $settings,
        private Request $request
    )
    {
    }

    function view(): View
    {
        return view('@f-components/authentication-providers', [
            'settings' => $this->settings
        ]);
    }
}