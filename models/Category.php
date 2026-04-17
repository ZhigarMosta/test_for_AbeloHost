<?php

namespace models;

use core\Database;

class Category
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        return $this->db->query(
            'SELECT c.*
             FROM category c
             ORDER BY c.id DESC'
        );
    }

    public function getAllWithArticleCategory(): array
    {
        return $this->db->query(
            'SELECT c.*, COUNT(ac.article_id) as article_count
             FROM category c
             LEFT JOIN article_category ac ON c.id = ac.category_id
             GROUP BY c.id
             ORDER BY c.id DESC'
        );
    }

    public function getAllWithArticles(): array
    {
        $categories = $this->db->query(
            'SELECT c.*, COUNT(ac.article_id) as article_count
             FROM category c
             LEFT JOIN article_category ac ON c.id = ac.category_id
             GROUP BY c.id
             HAVING article_count > 0
             ORDER BY c.id DESC'
        );

        foreach ($categories as &$category) {
            $category['articles'] = $this->getArticlesByCategory($category['id'], 3);
        }

        return $categories;
    }

    public function getById(int $id): ?array
    {
        $result = $this->db->query('SELECT * FROM category WHERE id = ?', [$id]);
        return $result[0] ?? null;
    }

    public function getArticlesByCategoryPaginated(int $categoryId, int $page = 1, int $perPage = 6, string $sort = 'date'): array
    {
        $offset = ($page - 1) * $perPage;
        $orderBy = $sort === 'views' ? 'a.views DESC' : 'a.created_at DESC';

        $articles = $this->db->query(
            "SELECT a.* FROM article a
             JOIN article_category ac ON a.id = ac.article_id
             WHERE ac.category_id = ?
             ORDER BY $orderBy
             LIMIT ? OFFSET ?",
            [$categoryId, $perPage, $offset]
        );

        return $articles;
    }

    public function getArticlesCountByCategory(int $categoryId): int
    {
        $result = $this->db->query(
            'SELECT COUNT(*) as count FROM article a
             JOIN article_category ac ON a.id = ac.article_id
             WHERE ac.category_id = ?',
            [$categoryId]
        );

        return (int)($result[0]['count'] ?? 0);
    }

    public function getArticlesByCategory(int $categoryId, int $limit = 3): array
    {
        return $this->db->query(
            'SELECT a.* FROM article a
             JOIN article_category ac ON a.id = ac.article_id
             WHERE ac.category_id = ?
             ORDER BY a.created_at DESC
             LIMIT ?',
            [$categoryId, $limit]
        );
    }

    public function create(string $name, ?string $description): int
    {
        return $this->db->execute(
            'INSERT INTO category (name, description) VALUES (?, ?)',
            [$name, $description]
        );
    }

    public function update(int $id, string $name, ?string $description): int
    {
        return $this->db->execute(
            'UPDATE category SET name = ?, description = ? WHERE id = ?',
            [$name, $description, $id]
        );
    }

    public function delete(int $id): int
    {
        return $this->db->execute('DELETE FROM category WHERE id = ?', [$id]);
    }

    public function deleteWithArticles(int $id): void
    {
        $articles = $this->db->query(
            'SELECT a.id, a.image FROM article a
             JOIN article_category ac ON a.id = ac.article_id
             WHERE ac.category_id = ?',
            [$id]
        );

        foreach ($articles as $article) {
            $countResult = $this->db->query(
                'SELECT COUNT(*) as count FROM article_category WHERE article_id = ?',
                [$article['id']]
            );
            $count = (int)($countResult[0]['count'] ?? 0);

            if ($count === 1) {
                if ($article['image']) {
                    $filePath = dirname(__DIR__, 2) . '/test/public' . $article['image'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                $this->db->execute('DELETE FROM article WHERE id = ?', [$article['id']]);
            }
        }

        $this->db->execute('DELETE FROM article_category WHERE category_id = ?', [$id]);
        $this->db->execute('DELETE FROM category WHERE id = ?', [$id]);
    }
}
