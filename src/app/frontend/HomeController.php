<?php

namespace app\frontend;

use Controller;
use infrastructure\ModuleRouter;
use Route;
use View;

#[Controller]
readonly class HomeController
{
    public function __construct(
        private ModuleRouter $homeRouter)
    { }

    #[Route("/")]
    public function index(): View
    {
        return $this->homeRouter->homepage();
    }
}
