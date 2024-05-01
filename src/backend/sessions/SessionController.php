<?php

namespace backend\sessions;
use Controller;
use infrastructure\settings\Settings;
use Request;
use Route;
use View;

import ('@/backend/sessions/*');

#[Controller]
readonly  class SessionController
{
    public function __construct (
        private Settings $settings,
        private Request $request,
        private SessionFinder $sessionFinder
    ) { }

    #[Route("/admin/sessions")]
    public function index(): View
    {
        $criteria = new Criteria();
        $criteria->setPage($this->request->getParameter("page", 1));
        $criteria->setPageSize($this->settings->pageSize);
        $sessions = $this->sessionFinder->find($criteria);
        $count = $this->sessionFinder->count($criteria);

        return view('@backend/sessions/index', ['sessions' => $sessions, 'count' => $count]);
    }
}