<?php

namespace app\frontend\community;

use Controller;
use Route;
use View;

#[Controller]
readonly class CommunityController
{
    #[Route("/c")]
    public function community(): View
    {
        return view('@frontend/community/index');
    }
}