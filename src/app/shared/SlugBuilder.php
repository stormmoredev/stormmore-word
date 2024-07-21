<?php

namespace app\shared;

use app\shared\domain\CategoryRepository;
use app\shared\domain\EntryRepository;
use infrastructure\Slug;

readonly class SlugBuilder
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntryRepository $entryRepository
    ) { }

    public function buildUniqueCategorySlug(string $name): string
    {
        $slug = Slug::slugify($name);
        while($this->categoryRepository->getBySlug($slug) != null) {
            $slug = $this->incrementSlug($slug);
        }
        return $slug;
    }

    public function buildUniqueEntrySlug(string $name): string
    {
        $slug = Slug::slugify($name);
        while($this->entryRepository->getBySlug($slug) != null) {
            $slug = $this->incrementSlug($slug);
        }
        return $slug;
    }

    private function incrementSlug(string $slug): string
    {
        $parts = explode('-', $slug);
        $end =  end($parts);
        if (is_numeric($end)) {
            $parts[count($parts) - 1] = $end + 1;
        } else {
            $parts[] = 1;
        }
        return implode('-', $parts);
    }
}