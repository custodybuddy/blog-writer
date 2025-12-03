<?php
namespace App\Repositories;

use PDO;

class TopicRepository
{
    public function __construct(private PDO $pdo, private string $topicsPath)
    {
    }

    public function seedFromFileIfEmpty(): void
    {
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM topics')->fetchColumn();
        if ($count > 0) {
            return;
        }

        if (!file_exists($this->topicsPath)) {
            return;
        }

        $topics = json_decode(file_get_contents($this->topicsPath), true) ?: [];
        $stmt = $this->pdo->prepare('INSERT INTO topics (title, description, used) VALUES (:title, :description, 0)');

        foreach ($topics as $topic) {
            $stmt->execute([
                ':title' => $topic['title'],
                ':description' => $topic['description'],
            ]);
        }
    }

    public function nextUnused(): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM topics WHERE used = 0 ORDER BY id LIMIT 1');
        $stmt->execute();
        $topic = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$topic) {
            return null;
        }

        return $topic;
    }

    public function markAsUsed(int $id): void
    {
        $markUsed = $this->pdo->prepare('UPDATE topics SET used = 1 WHERE id = :id');
        $markUsed->execute([':id' => $id]);
    }
}
