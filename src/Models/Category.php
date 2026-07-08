<?php

namespace App\Models;

use App\Database;
use PDO;

class Category
{
    public static function all(): array
    {
        return Database::connection()
            ->query('SELECT * FROM categories ORDER BY name')
            ->fetchAll();
    }

    public static function withArticles(): array
    {
        $sql = 'SELECT c.*
                FROM categories c
                WHERE EXISTS (
                    SELECT 1 FROM article_category ac WHERE ac.category_id = c.id
                )
                ORDER BY c.name';

        return Database::connection()->query($sql)->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);

        $category = $stmt->fetch();

        return $category ?: null;
    }
}
