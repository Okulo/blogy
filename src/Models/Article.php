<?php

namespace App\Models;

use App\Database;
use PDO;

class Article
{
    private const SORTS = [
        'date' => 'a.published_at',
        'views' => 'a.views',
    ];

    public static function latestByCategory(int $categoryId, int $limit): array
    {
        $sql = 'SELECT a.*
                FROM articles a
                JOIN article_category ac ON ac.article_id = a.id
                WHERE ac.category_id = :category_id
                ORDER BY a.published_at DESC, a.id DESC
                LIMIT :limit';

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function paginateByCategory(int $categoryId, string $sort, int $page, int $perPage): array
    {
        $orderBy = self::SORTS[$sort] ?? self::SORTS['date'];
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT a.*
                FROM articles a
                JOIN article_category ac ON ac.article_id = a.id
                WHERE ac.category_id = :category_id
                ORDER BY {$orderBy} DESC, a.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function countByCategory(int $categoryId): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM article_category WHERE category_id = :category_id'
        );
        $stmt->execute(['category_id' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM articles WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);

        $article = $stmt->fetch();

        return $article ?: null;
    }

    public static function categoriesOf(int $articleId): array
    {
        $sql = 'SELECT c.*
                FROM categories c
                JOIN article_category ac ON ac.category_id = c.id
                WHERE ac.article_id = :article_id
                ORDER BY c.name';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['article_id' => $articleId]);

        return $stmt->fetchAll();
    }

    public static function similar(int $articleId, array $categoryIds, int $limit): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $sql = "SELECT a.*, COUNT(*) AS shared
                FROM articles a
                JOIN article_category ac ON ac.article_id = a.id
                WHERE ac.category_id IN ({$placeholders})
                  AND a.id <> ?
                GROUP BY a.id
                ORDER BY shared DESC, a.published_at DESC
                LIMIT ?";

        $stmt = Database::connection()->prepare($sql);

        $position = 1;
        foreach ($categoryIds as $categoryId) {
            $stmt->bindValue($position++, $categoryId, PDO::PARAM_INT);
        }
        $stmt->bindValue($position++, $articleId, PDO::PARAM_INT);
        $stmt->bindValue($position, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function incrementViews(int $articleId): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE articles SET views = views + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $articleId]);
    }
}
