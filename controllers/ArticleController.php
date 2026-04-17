<?php

namespace controllers;

use models\Article;
use models\Category;

class ArticleController
{
    private Article $articleModel;
    private Category $categoryModel;
    private \Smarty $smarty;
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct()
    {
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
        $this->smarty = \core\SmartySetup::getInstance();
    }

    public function view(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $article = $this->articleModel->getById($id);

        if (!$article) {
            http_response_code(404);
            return;
        }

        $this->articleModel->incrementViews($id);

        $categoryIds = !empty($article['category_ids']) ? explode(',', $article['category_ids']) : [];
        $relatedArticles = $this->articleModel->getRelatedArticles($id, $categoryIds);

        $this->smarty->assign('article', $article);
        $this->smarty->assign('relatedArticles', $relatedArticles);
        $this->smarty->display('article/view.tpl');
    }

    public function showCreate(): void
    {
        $categories = $this->categoryModel->getAll();
        $this->smarty->assign('categories', $categories);
        $this->smarty->display('article/create.tpl');
    }

    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');

        if (empty($title)) {
            header('Location: index.php?controller=article&action=showCreate');
            exit;
        }

        $image = $this->uploadImage();

        $data = [
            'image' => $image ?? '',
            'title' => $title,
            'description' => $_POST['description'] ?? '',
            'content' => $_POST['content'] ?? '',
        ];
        $categoryIds = $_POST['categories'] ?? [];

        $this->articleModel->create($data, $categoryIds);
        header('Location: index.php?controller=category');
        exit;
    }

    public function showEdit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $article = $this->articleModel->getByIdWithCategories($id);
        
        if (!$article) {
            http_response_code(404);
            return;
        }
        
        $categories = $this->categoryModel->getAllWithArticleCategory();
        $this->smarty->assign('article', $article);
        $this->smarty->assign('categories', $categories);
        $this->smarty->display('article/edit.tpl');
    }

    public function update(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');

        if (empty($title)) {
            header("Location: index.php?controller=article&action=showEdit&id=$id");
            exit;
        }

        $image = $this->uploadImage();
        $existingArticle = $this->articleModel->getById($id);
        
        $data = [
            'image' => $image ?? $existingArticle['image'] ?? '',
            'title' => $title,
            'description' => $_POST['description'] ?? '',
            'content' => $_POST['content'] ?? '',
        ];
        $categoryIds = $_POST['categories'] ?? [];
        
        $this->articleModel->update($id, $data, $categoryIds);
        header('Location: index.php?controller=category');
        exit;
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $article = $this->articleModel->getById($id);
        
        if ($article && $article['image']) {
            $filePath = dirname(__DIR__, 2) . '/test/public' . $article['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $this->articleModel->delete($id);
        header('Location: index.php?controller=category');
        exit;
    }

    private function uploadImage(): ?string
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $fileType = $_FILES['image']['type'];

        if (!in_array($fileType, $this->allowedTypes)) {
            return null;
        }

        $uploadDir = '/var/www/html/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . strtolower($extension);
        $targetPath = $uploadDir . $filename;

        if (@copy($_FILES['image']['tmp_name'], $targetPath)) {
            return '/uploads/' . $filename;
        }

        return null;
    }
}
