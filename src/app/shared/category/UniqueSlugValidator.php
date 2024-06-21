<?php

use app\shared\category\CategoryRepository;
use infrastructure\Slug;

readonly class CategorySlugValidator implements IValidator
{
    public function __construct(
        private Request            $request,
        private CategoryRepository $categoryRepository)
    { }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        $id = $this->request->get('id');
        $slug = Slug::slugify($value);
        $category = $this->categoryRepository->getBySlug($slug);
        if ($category != null and $category->id != $id)
            return new ValidatorResult(false, _("Slug already exist."));
        return new ValidatorResult();
    }
}