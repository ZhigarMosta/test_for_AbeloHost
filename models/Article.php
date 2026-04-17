<?php

namespace models;

use core\Database;

class Article
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->query(
            'SELECT a.*, GROUP_CONCAT(c.name) as categories, GROUP_CONCAT(c.id) as category_ids
             FROM article a
             LEFT JOIN article_category ac ON a.id = ac.article_id
             LEFT JOIN category c ON ac.category_id = c.id
             GROUP BY a.id
             ORDER BY a.created_at DESC'
        );
    }

    public function getById(int $id): ?array{
        $result = $this->db->query(
            'SELECT a.*
             FROM article a
             WHERE a.id = ?',
            [$id]
        );
        return $result[0] ?? null;
    }

    public function getByIdWithCategories(int $id): ?array
    {
        $result = $this->db->query(
            'SELECT a.*, GROUP_CONCAT(c.id) as category_ids
             FROM article a
             LEFT JOIN article_category ac ON a.id = ac.article_id
             LEFT JOIN category c ON ac.category_id = c.id
             WHERE a.id = ?
             GROUP BY a.id',
            [$id]
        );
        return $result[0] ?? null;
    }

    public function getByCategory(int $categoryId): array
    {
        return $this->db->query(
            'SELECT a.* FROM article a
             JOIN article_category ac ON a.id = ac.article_id
             WHERE ac.category_id = ?
             ORDER BY a.created_at DESC',
            [$categoryId]
        );
    }

    public function create(array $data, array $categoryIds = []): int
    {
        $this->db->execute(
            'INSERT INTO article (image, title, description, content, views) VALUES (?, ?, ?, ?, ?)',
            [$data['image'] ?? '', $data['title'], $data['description'] ?? '', $data['content'] ?? '', $data['views'] ?? 0]
        );
        
        $articleId = (int)$this->db->lastInsertId();
        $this->saveCategories($articleId, $categoryIds);
        
        return $articleId;
    }

    public function update(int $id, array $data, array $categoryIds = []): int
    {
        $this->db->execute(
            'UPDATE article SET image = ?, title = ?, description = ?, content = ?, views = ? WHERE id = ?',
            [$data['image'] ?? '', $data['title'], $data['description'] ?? '', $data['content'] ?? '', $data['views'] ?? 0, $id]
        );
        
        $this->saveCategories($id, $categoryIds);
        
        return $id;
    }

    public function delete(int $id): int
    {
        return $this->db->execute('DELETE FROM article WHERE id = ?', [$id]);
    }

    public function incrementViews(int $id): void
    {
        $this->db->execute('UPDATE article SET views = views + 1 WHERE id = ?', [$id]);
    }

    public function getRecent(int $limit = 5): array
    {
        return $this->db->query(
            'SELECT a.* FROM article a ORDER BY a.created_at DESC LIMIT ?',
            [$limit]
        );
    }

    public function getRelatedArticles(int $articleId, array $categoryIds, int $limit = 3): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $articles = $this->db->query(
            "SELECT a.* FROM article a
             JOIN article_category ac ON a.id = ac.article_id
             WHERE ac.category_id IN ($placeholders)
             AND a.id != ?
             GROUP BY a.id
             ORDER BY a.created_at DESC",
            array_merge($categoryIds, [$articleId])
        );

        $currentArticle = $this->getByIdWithCategories($articleId);
        if (!$currentArticle) {
            return array_slice($articles, 0, $limit);
        }

        $currentTitle = mb_strtolower($currentArticle['title']);

        usort($articles, function ($a, $b) use ($currentTitle) {
            $simA = similar_text($currentTitle, mb_strtolower($a['title']));
            $simB = similar_text($currentTitle, mb_strtolower($b['title']));
            return $simB <=> $simA;
        });

        $related = [];
        foreach ($articles as $article) {
            $similarity = similar_text($currentTitle, mb_strtolower($article['title']));
            $percent = ($similarity / mb_strlen($currentTitle)) * 100;
            
            if ($percent >= 50) {
                $related[] = $article;
            }
            
            if (count($related) >= $limit) {
                break;
            }
        }

        return $related;
    }

    private function saveCategories(int $articleId, array $categoryIds): void
    {
        $this->db->execute('DELETE FROM article_category WHERE article_id = ?', [$articleId]);
        
        foreach ($categoryIds as $categoryId) {
            $this->db->execute(
                'INSERT INTO article_category (article_id, category_id) VALUES (?, ?)',
                [$articleId, (int)$categoryId]
            );
        }
    }
}
