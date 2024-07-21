<?php

namespace app\frontend\blog;

use Form;
use infrastructure\title_media\TitledMediaValidator;

class AddPostForm extends Form
{
    public function __construct($request)
    {
        parent::__construct($request);

        $rules = [
            'title' => [
                'required' => ['message' => _('post_title_required')],
                'max_length' => 128
            ],
            'subtitle' => [
                'required' => ['message' => _('post_subtitle_required')],
                'max_length' => 256
            ],
            'content' => ['required' => ['message' => _('post_content_required')],
                'max_length' => 10000
            ],
            'media' => [TitledMediaValidator::class]
        ];
        $this->addRules($rules);
    }
}