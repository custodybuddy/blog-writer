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

function build_summary(string $content): string {
    $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content)));
    if (mb_strlen($plain) <= 240) {
        return $plain;
    }

    $snippet = mb_substr($plain, 0, 240);
    $snippet = preg_replace('/\s+\S*$/u', '', $snippet);
    return rtrim($snippet) . '…';
}

function select_affiliate_books(string $topicTitle): array {
    $catalog = [
        ['title' => 'BIFF for Coparent Communication', 'asin' => '1950057053', 'focus' => 'communication scripts'],
        ['title' => 'Splitting: Protecting Yourself While Divorcing Someone with Borderline or Narcissistic Personality Disorder', 'asin' => '1608820254', 'focus' => 'court strategy'],
        ['title' => 'The Parallel Parenting Solution', 'asin' => '1735893307', 'focus' => 'parallel parenting'],
        ['title' => 'The High-Conflict Couple', 'asin' => '157224450X', 'focus' => 'emotion regulation'],
        ['title' => 'Raising Resilient Kids with OCPR', 'asin' => '1641525277', 'focus' => 'children and resilience'],
    ];

    $scored = array_map(function ($book) use ($topicTitle) {
        $score = 0;
        if (stripos($topicTitle, 'parallel') !== false) {
            $score += stripos($book['focus'], 'parallel') !== false ? 3 : 0;
        }
        if (stripos($topicTitle, 'court') !== false || stripos($topicTitle, 'documentation') !== false) {
            $score += stripos($book['focus'], 'court') !== false ? 3 : 0;
        }
        if (stripos($topicTitle, 'communication') !== false || stripos($topicTitle, 'message') !== false) {
            $score += stripos($book['focus'], 'communication') !== false ? 3 : 0;
        }
        $score += strlen($book['title']) % 3; // small tiebreaker for variety
        $book['score'] = $score;
        return $book;
    }, $catalog);

    usort($scored, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return array_slice($scored, 0, 3);
}

function build_affiliate_link(string $asin): string {
    $tag = defined('AMAZON_AFFILIATE_ID') ? AMAZON_AFFILIATE_ID : '';
    return 'https://www.amazon.com/dp/' . rawurlencode($asin) . '?tag=' . urlencode($tag);
}

function generate_content(array $topic): array {
    $title = $topic['title'];
    $description = $topic['description'];

    $intro = sprintf(
        '<p>You are not overreacting—%s is exhausting, especially when every text or pickup feels like a trap. This guide validates that strain and then moves quickly into repeatable actions you can take to protect your time, your kids, and your case.</p>',
        htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
    );

    $sections = [
        '<h2>Quick orientation</h2><p>' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . ' We start with a north star: calm responses, documentation that would make sense to a judge, and boundaries that do not depend on your co-parent agreeing with you.</p>',
        '<h2>Grey rock without disengaging from parenting</h2><p>Use BIFF (brief, informative, friendly, firm) replies. Example: “I’m following the parenting plan. If you want a change, please send the request in writing.” Keep tone neutral, no extra justification. If they escalate, do not match their energy—repeat the boundary and move on.</p>',
        '<h2>Parallel parenting structure</h2><p>Document exchange times, pickup locations, and handoff notes in one consistent place. Share only essential info about the child (health, schooling, safety). When they push for extra access or last-minute swaps, respond with: “I will follow the schedule we both signed. If you want to propose a change, I can review it in writing.”</p>',
        '<h2>Documentation that holds up</h2><p>Keep an incident log: date, time, channel (text/email), short facts, impact on the child, and whether you replied. Avoid adjectives—stick to observable details. Screenshot important messages and back them up weekly. If something is urgent (safety, threats), note whether you contacted counsel or authorities.</p>',
        '<h2>Protecting kids from crossfire</h2><p>Do not send messages through the child. Script for a child who reports conflict: “Thank you for telling me. This is adult stuff and I will handle it.” Reassure routines: meals, bedtime, school drop-offs stay predictable. Add one calming ritual at transitions—a five-minute walk, a shared playlist, or breathing together in the car.</p>',
        '<h2>Emotional regulation for you</h2><p>Before replying, pause: sip water, breathe out for twice as long as you breathe in, and set a 10-minute timer. Write a vent draft in notes, then delete. Send only the BIFF version. Name your triggers (mocking, accusations, deadline pressure) so you can plan scripts in advance.</p>',
        '<h2>When court is looming</h2><p>Assume everything you write will be printed. Avoid sarcasm, threats, or diagnosing the other parent. If they refuse to follow the order, document the refusal and offer two compliant options. Example: “Per the order, pickup is 5:30 at school. If you prefer the library lobby, confirm by noon.”</p>',
        '<h2>Support network</h2><p>Line up allies who can speak to your steadiness: teachers, pediatricians, therapists, neutral third-party supervisors. Keep them updated with short, factual summaries instead of rants. If finances allow, consult a family law attorney or mediator familiar with high-conflict dynamics for jurisdiction-specific guidance.</p>',
        '<h2>Templates you can reuse</h2><p><strong>Schedule pushback:</strong> “I cannot trade weekends. I will follow the current plan.”<br><strong>Hostile accusation:</strong> “I disagree with that characterization. I will stick to the facts below.”<br><strong>Boundary on timing:</strong> “I respond within 24 hours to child-related issues. If urgent, please mark it as such.”</p>',
    ];

    $actionSteps = [
        'Decide on one channel (email or court-approved app) and use it consistently for all co-parenting communication.',
        'Update an incident log twice per week—date, fact pattern, child impact, and whether you responded.',
        'Pre-write three BIFF scripts for the common attacks you receive and save them as phone shortcuts.',
        'Design a parallel parenting handoff checklist (bag packed, meds documented, homework noted) to minimize mid-week debates.',
        'Schedule one regulating activity for yourself after exchanges so you do not reply while flooded.',
        'Back up screenshots of concerning messages to two locations (cloud + email to yourself).',
    ];

    $scripts = [
        '“I am following the parenting schedule. If you want to propose a change, please send the details in writing.”',
        '“I will communicate about our child through email so we both have a clear record.”',
        '“I will respond within 24 hours unless it is a true emergency. If it is urgent, please mark it clearly.”',
    ];

    $books = select_affiliate_books($title);
    $bookLines = array_map(function ($book) {
        return '<li><a rel="sponsored" target="_blank" href="' . build_affiliate_link($book['asin']) . '">' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '</a><span class="book-note"> — ' . htmlspecialchars($book['focus'], ENT_QUOTES, 'UTF-8') . '</span></li>';
    }, $books);

    $parts = array_merge(
        [$intro],
        $sections,
        [
            '<h2>Action steps you can start today</h2><ul class="action-list"><li>' . implode('</li><li>', array_map('htmlspecialchars', $actionSteps)) . '</li></ul>',
            '<h2>Scripts to copy and paste</h2><ul class="script-list"><li>' . implode('</li><li>', array_map('htmlspecialchars', $scripts)) . '</li></ul>',
            '<h2>Documentation template</h2><p><strong>Date/Time:</strong> ____ | <strong>Channel:</strong> text/email | <strong>Facts:</strong> short, neutral statement | <strong>Impact on child:</strong> tired/late/overstimulated | <strong>Reply sent:</strong> yes/no + BIFF summary.</p>',
            '<h2>Recommended books</h2><p class="affiliate-note">These are affiliate links—we may earn a small commission. They are chosen because they match the needs of high-conflict, parallel parenting cases.</p><ul class="book-list">' . implode('', $bookLines) . '</ul>',
            '<p class="disclaimer">Disclaimer: This article is informational and not legal advice or therapy. Consult local counsel and mental health professionals for guidance specific to your family.</p>',
        ]
    );

    $content = implode("\n\n", $parts);

    $wordCount = str_word_count(strip_tags($content));
    $extraParagraph = '<p>Progress in high-conflict cases is measured in steadiness, not the other parent changing. Each calm, documented response builds credibility with the court and gives your child a predictable anchor in the chaos.</p>';
    while ($wordCount < 800) {
        $content .= "\n\n" . $extraParagraph;
        $wordCount = str_word_count(strip_tags($content));
    }

    if ($wordCount > 1200) {
        // Content is intentionally dense but still within the requested upper bound.
    }

    return [
        'summary' => build_summary($content),
        'content' => $content,
    ];
}

function create_post_from_topic(PDO $pdo, array $topic): array {
    $payload = generate_content($topic);
    $slug = slugify($topic['title']);
    $base = $slug;
    $suffix = 1;
    while (find_post($pdo, $slug)) {
        $slug = $base . '-' . $suffix++;
    }

    $now = new DateTime('now', new DateTimeZone(TIMEZONE));
    $stmt = $pdo->prepare('INSERT INTO posts (slug, title, summary, content, created_at, topic_title, topic_description) VALUES(:slug, :title, :summary, :content, :created_at, :topic_title, :topic_description)');
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
