<?php

use App\Database;

require dirname(__DIR__) . '/vendor/autoload.php';

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('~[^a-z0-9]+~', '-', $value);

    return trim($value, '-');
}

$categories = [
    ['name' => 'Design', 'description' => 'Visual thinking, interfaces and the craft behind good-looking products.'],
    ['name' => 'Technology', 'description' => 'Notes on tools, engineering practices and the web platform.'],
    ['name' => 'Travel', 'description' => 'Places worth the trip and lessons learned on the road.'],
    ['name' => 'Food', 'description' => 'Recipes, ingredients and slow mornings in the kitchen.'],
    ['name' => 'Lifestyle', 'description' => 'Habits, focus and a calmer way to spend the day.'],
];

$titles = [
    'Design' => [
        'Rethinking minimal interfaces',
        'The quiet power of white space',
        'Type systems that scale',
        'Designing for the first five seconds',
        'Color palettes that age well',
        'Grids are still your best friend',
        'When to break your own rules',
        'Icons that actually communicate',
    ],
    'Technology' => [
        'Small tools, big leverage',
        'Reading code before writing it',
        'The case for boring technology',
        'Caching without the headaches',
        'A gentle guide to observability',
        'Why your build is slow',
        'Databases you can reason about',
        'Shipping on Fridays, carefully',
    ],
    'Travel' => [
        'A slow week in the mountains',
        'Packing light for two weeks',
        'Trains beat planes sometimes',
        'The best coffee is always local',
        'Getting lost on purpose',
        'Coastlines worth the detour',
        'One city, three neighborhoods',
        'Off-season and loving it',
    ],
    'Food' => [
        'A better weeknight pasta',
        'Bread that forgives beginners',
        'The five-ingredient rule',
        'Soups for cold evenings',
        'Learning to trust your knife',
        'Breakfast worth waking up for',
        'Pantry staples that never fail',
        'Slow mornings, good eggs',
    ],
    'Lifestyle' => [
        'A calmer morning routine',
        'Doing less, but better',
        'Notebooks over apps',
        'The two-minute reset',
        'Focus is a practice',
        'Saying no with grace',
        'Small habits, real change',
        'Evenings without a screen',
    ],
];

$paragraphs = [
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quo sunt tempora dolor laudantium sed optio, explicabo ad deleniti impedit facilis fugit recusandae illo, aliquid, dicta beatae quia porro id est.',
    'Praesentium voluptatibus repellat, tenetur nihil ratione dolorem quibusdam. Sit amet consectetur adipisicing elit, eaque quas molestiae quos dolor blanditiis, cum voluptatum nam sapiente.',
    'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto.',
    'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt neque porro quisquam.',
    'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati.',
];

$pdo = Database::connection();

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('TRUNCATE TABLE article_category');
$pdo->exec('TRUNCATE TABLE articles');
$pdo->exec('TRUNCATE TABLE categories');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$insertCategory = $pdo->prepare(
    'INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)'
);

$categoryIds = [];
foreach ($categories as $category) {
    $insertCategory->execute([
        'name' => $category['name'],
        'slug' => slugify($category['name']),
        'description' => $category['description'],
    ]);
    $categoryIds[$category['name']] = (int) $pdo->lastInsertId();
}

$insertArticle = $pdo->prepare(
    'INSERT INTO articles (title, slug, description, body, image, views, published_at)
     VALUES (:title, :slug, :description, :body, :image, :views, :published_at)'
);
$insertPivot = $pdo->prepare(
    'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)'
);

$usedSlugs = [];
$imageIndex = 0;
$categoryNames = array_keys($categoryIds);

foreach ($titles as $categoryName => $pool) {
    foreach ($pool as $title) {
        $slug = slugify($title);
        while (isset($usedSlugs[$slug])) {
            $slug .= '-2';
        }
        $usedSlugs[$slug] = true;

        $body = implode("\n\n", array_slice($paragraphs, 0, random_int(3, count($paragraphs))));
        $image = 'post-' . (($imageIndex++ % 6) + 1) . '.jpg';
        $published = (new DateTimeImmutable())->modify('-' . random_int(0, 120) . ' days');

        $insertArticle->execute([
            'title' => $title,
            'slug' => $slug,
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quo sunt tempora dolor laudantium sed optio.',
            'body' => $body,
            'image' => $image,
            'views' => random_int(0, 5000),
            'published_at' => $published->format('Y-m-d H:i:s'),
        ]);

        $articleId = (int) $pdo->lastInsertId();
        $assigned = [$categoryName];

        $insertPivot->execute([
            'article_id' => $articleId,
            'category_id' => $categoryIds[$categoryName],
        ]);

        if (random_int(1, 100) <= 35) {
            $others = array_values(array_diff($categoryNames, $assigned));
            $second = $others[array_rand($others)];
            $insertPivot->execute([
                'article_id' => $articleId,
                'category_id' => $categoryIds[$second],
            ]);
        }
    }
}

$articleCount = (int) $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
$categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();

echo "Seeded {$categoryCount} categories and {$articleCount} articles." . PHP_EOL;
