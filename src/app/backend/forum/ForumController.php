<?php

namespace app\backend\forum;

use Controller;
use Form;
use infrastructure\Categories;
use Redirect;
use Request;
use Route;
use View;

#[Controller]
class ForumController
{
    private array   $categoryValidationRules;

    public function __construct (
        private readonly Request                 $request,
        private readonly ThreadFinder            $threadFinder,
        private readonly ForumCategoryFinder     $forumCategoryFinder,
        private readonly ForumCategoryRepository $forumCategoryRepository,
        private readonly ForumService            $forumService
    )
    {
        $this->categoryValidationRules = [
            'name'  =>          ['required', 'category-slug', 'maxlen' => 256],
            'sequence' =>       ['required', 'int'],
            'description' =>    ['maxlen' => 1024]];
    }

    #[Route("/admin/forum/threads", "/admin/forum")]
    public function threads(): View
    {
        $threads = $this->threadFinder->find();
        return view('@backend/forum/threads', ['threads' => $threads]);
    }

    #[Route("/admin/forum/categories")]
    public function categories(): View
    {
        $categories = new Categories($this->forumCategoryFinder->find());
        return view('@backend/forum/categories', ['categories' => $categories]);
    }

    #[Route("/admin/forum/categories/add")]
    public function addCategory(): View|Redirect
    {
        $categories = new Categories($this->forumCategoryFinder->find());
        $form = new Form($this->request);
        $form->rules = $this->categoryValidationRules;
        if ($form->isSubmittedSuccessfully())
        {
            $parameters = $this->request->get('name', 'sequence', 'description', 'parent_id');
            list($name, $sequence, $description, $parent_id) = $parameters;
            $this->forumService->addCategory($name, $sequence, $description, $parent_id);
            return redirect('/admin/forum/categories');
        }

        return view('@backend/forum/category-add', ['categories' => $categories, 'form' => $form]);
    }

    #[Route("/admin/forum/categories/edit")]
    public function editCategory(): View|Redirect
    {
        $id = $this->request->get('id');
        $form = new Form($this->request);
        $form->rules = $this->categoryValidationRules;
        if ($form->isSubmittedSuccessfully())
        {
            $parameters = $this->request->get('name', 'sequence', 'description', 'parent_id');
            list($name, $sequence, $description, $parent_id) = $parameters;
            $this->forumService->updateCategory($id, $name, $sequence, $description, $parent_id);
            return redirect('/admin/forum/categories');
        }

        $category = $this->forumCategoryRepository->getById($id);
        $categories = new Categories($this->forumCategoryFinder->find());
        $viewData = ['categories' => $categories, 'category' => $category, 'form' => $form];
        return view('@backend/forum/category-edit', $viewData);
    }

    #[Route("/admin/forum/categories/delete")]
    public function deleteCategory(): Redirect
    {
        $id = $this->request->get('id');
        $this->forumService->deleteCategory($id);
        return redirect('/admin/forum/categories');
    }
}