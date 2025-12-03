<?php
namespace App\Repositories;

use DateTime;
use DateTimeZone;
use PDO;

class PostRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function latest(int $limit = 20): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts ORDER BY datetime(created_at) DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE slug = :slug');
        $stmt->execute([':slug' => $slug]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        return $post ?: null;
    }

    public function slugExists(string $slug): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM posts WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data, DateTimeZone $timezone): array
    {
        $now = new DateTime('now', $timezone);

        $stmt = $this->pdo->prepare('INSERT INTO posts (slug, title, summary, content, created_at, topic_title, topic_description) VALUES (:slug, :title, :summary, :content, :created_at, :topic_title, :topic_description)');
        $stmt->execute([
            ':slug' => $data['slug'],
            ':title' => $data['title'],
            ':summary' => $data['summary'],
            ':content' => $data['content'],
            ':created_at' => $now->format('Y-m-d H:i:s'),
            ':topic_title' => $data['topic_title'] ?? $data['title'],
            ':topic_description' => $data['topic_description'] ?? '',
        ]);

        return $this->findBySlug($data['slug']);
    }
}
