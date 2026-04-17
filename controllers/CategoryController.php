<?php

namespace controllers;

use models\Category;

class CategoryController
{
    private Category $categoryModel;
    private \Smarty $smarty;
    private int $perPage = 5;

    public function __construct()
    {
        $this->categoryModel = new \models\Category();
        $this->smarty = \core\SmartySetup::getInstance();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getAllWithArticles();
        $this->smarty->assign('categories', $categories);
        $this->smarty->display('category/index.tpl');
    }

    public function view(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            http_response_code(404);
            echo 'Category not found';
            return;
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $sort = $_GET['sort'] ?? 'date';
        
        if (!in_array($sort, ['date', 'views'])) {
            $sort = 'date';
        }
        
        $totalArticles = $this->categoryModel->getArticlesCountByCategory($id);
        $totalPages = max(1, (int)ceil($totalArticles / $this->perPage));
        $page = min($page, $totalPages);
        
        $articles = $this->categoryModel->getArticlesByCategoryPaginated($id, $page, $this->perPage, $sort);
        
        $this->smarty->assign('category', $category);
        $this->smarty->assign('articles', $articles);
        $this->smarty->assign('pagination', [
            'current' => $page,
            'total' => $totalPages,
            'sort' => $sort,
        ]);
        $this->smarty->display('category/view.tpl');
    }

    public function showCreate(): void
    {
        $this->smarty->display('category/create.tpl');
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            header('Location: index.php?controller=category&action=showCreate');
            exit;
        }

        $description = $_POST['description'] ?? null;
        $this->categoryModel->create($name, $description);
        header('Location: index.php?controller=category');
        exit;
    }

    public function showEdit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->categoryModel->getById($id);
        $this->smarty->assign('category', $category);
        $this->smarty->display('category/edit.tpl');
    }

    public function update(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            header("Location: index.php?controller=category&action=showEdit&id=$id");
            exit;
        }

        $description = $_POST['description'] ?? null;
        $this->categoryModel->update($id, $name, $description);
        header('Location: index.php?controller=category');
        exit;
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->categoryModel->delete($id);
        header('Location: index.php?controller=category');
        exit;
    }
}
