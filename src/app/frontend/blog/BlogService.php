<?php

namespace app\frontend\blog;

use app\authentication\StormUser;
use app\shared\EntryService;
use app\shared\SlugBuilder;
use infrastructure\settings\Settings;
use stdClass;

readonly class BlogService extends EntryService
{
    public function __construct(
        private Settings       $settings,
        private StormUser      $user,
        private BlogRepository $blogRepository,
        private SlugBuilder    $slugBuilder)
    {
        parent::__construct($this->blogRepository, $this->user);
    }

    public function addPost(object $post): string
    {
        $post->slug = $this->slugBuilder->buildUniqueEntrySlug($post->title);
        $post->author_id = $this->user->id;
        $post->language = $this->settings->defaultLanguage->primary;

        $id = $this->blogRepository->insertPost($post);
        $this->blogRepository->insertMediaTitle($id, $post->media);

        return $post->slug;
    }

    public function addComment(int $postId, string $content): ?int
    {
        if (!$this->settings->blog->comments->enabled) return null;
        if (strip_tags($content) > $this->settings->blog->comments->maxChars) return null;

        $approval = !$this->settings->blog->comments->approvalIsRequired;
        $comment = new stdClass();
        $comment->author_id = $this->user->id;
        $comment->article_id = $postId;
        $comment->content = $content;
        $comment->is_approved = $approval;

        $this->blogRepository->updateRepliesCounterAndLastActivityTime($postId);
        return $this->blogRepository->insertComment($comment);
    }
}