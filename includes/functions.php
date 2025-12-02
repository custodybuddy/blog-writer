<?php
require_once __DIR__ . '/db.php';

function ensure_topics_loaded(PDO $pdo): void {
    $count = (int) $pdo->query('SELECT COUNT(*) FROM topics')->fetchColumn();
    if ($count === 0) {
        $topics = json_decode(file_get_contents(__DIR__ . '/../topics.json'), true);
        $stmt = $pdo->prepare('INSERT INTO topics (title, description, used) VALUES (:title, :description, 0)');
        foreach ($topics as $topic) {
            $stmt->execute([
                ':title' => $topic['title'],
                ':description' => $topic['description'],
            ]);
        }
    }
}

function latest_posts(PDO $pdo, int $limit = 20): array {
    $stmt = $pdo->prepare('SELECT * FROM posts ORDER BY datetime(created_at) DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function find_post(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = :slug');
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    return $post ?: null;
}

function next_topic(PDO $pdo): ?array {
    $stmt = $pdo->prepare('SELECT * FROM topics WHERE used = 0 ORDER BY id LIMIT 1');
    $stmt->execute();
    $topic = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$topic) {
        return null;
    }
    $markUsed = $pdo->prepare('UPDATE topics SET used = 1 WHERE id = :id');
    $markUsed->execute([':id' => $topic['id']]);
    return $topic;
}

function slugify(string $text): string {
    $text = preg_replace('~[\p{Pd}\s]+~u', '-', $text);
    $text = preg_replace('~[^\pL\pN-]+~u', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    if ($text === '') {
        $text = 'post-' . time();
    }
    return $text;
}

function build_paragraph(string $topicTitle, string $topicDescription): string {
    return sprintf(
        '%s. %s This section offers a calm, actionable path forward—naming the pattern, validating the exhaustion, and giving the reader scripts they can try immediately, even in a hostile co-parenting dynamic. It adds a short example of what to document, what to say in one sentence, and how to reset expectations so the reader feels capable instead of overwhelmed.',
        $topicTitle,
        $topicDescription
    );
}

function generate_content(array $topic): array {
    $intro = "You're not imagining it—" . $topic['title'] . " is draining. This post validates that feeling, then moves quickly into steps you can actually take.";
    $sections = [];
    for ($i = 0; $i < 12; $i++) {
        $sections[] = build_paragraph($topic['title'], $topic['description']);
    }
    $actionSteps = [
        'Use BIFF-style responses to lower the temperature while documenting every exchange.',
        'Keep a simple incident log with date, time, neutral description, and impact on the child.',
        'Set boundaries on response time and channel (e.g., email only, 24-hour window).',
        'Share calm scripts like “I’m following the parenting plan. If you’d like a change, please propose it in writing.”',
        'Model regulation cues for kids—slow breathing, short check-ins, and predictable routines.',
    ];

    $books = [
        ['title' => 'BIFF for Coparent Communication', 'asin' => '1950057053'],
        ['title' => 'Splitting: Protecting Yourself While Divorcing Someone with Borderline or Narcissistic Personality Disorder', 'asin' => '1608820254'],
        ['title' => 'The Parallel Parenting Solution', 'asin' => '1735893307'],
    ];

    return [
        'summary' => substr($intro, 0, 220),
        'content' => $intro . "\n\n" . implode("\n\n", $sections) . "\n\nAction Steps:\n- " . implode("\n- ", $actionSteps) . "\n\nDisclaimer: This post is information, not legal or clinical advice."
            . "\n\nRecommended Reading:\n" . implode("\n", array_map(function ($book) {
                return $book['title'] . ' — https://www.amazon.com/dp/' . $book['asin'];
            }, $books)),
    ];
}

function create_post_from_topic(PDO $pdo, array $topic): array {
    $payload = generate_content($topic);
    $slug = slugify($topic['title']);
    $now = new DateTime('now', new DateTimeZone(TIMEZONE));
    $stmt = $pdo->prepare('INSERT INTO posts (slug, title, summary, content, created_at, topic_title, topic_description) VALUES (:slug, :title, :summary, :content, :created_at, :topic_title, :topic_description)');
    $stmt->execute([
        ':slug' => $slug,
        ':title' => $topic['title'],
        ':summary' => $payload['summary'],
        ':content' => $payload['content'],
        ':created_at' => $now->format('Y-m-d H:i:s'),
        ':topic_title' => $topic['title'],
        ':topic_description' => $topic['description'],
    ]);
    return find_post($pdo, $slug);
}
?>
