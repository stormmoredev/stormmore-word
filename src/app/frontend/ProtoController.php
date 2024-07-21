<?php

namespace app\frontend;

use Controller;
use infrastructure\ModuleRouter;
use Route;
use View;

#[Controller]
readonly class ProtoController
{
    public function __construct(
        private ModuleRouter $homeRouter)
    { }

    #[Route("/p")]
    public function index(): View
    {
        return $this->homeRouter->homepage();
    }
}
