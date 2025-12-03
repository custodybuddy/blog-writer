<?php
namespace App\Services;

use App\Repositories\PostRepository;
use App\Repositories\TopicRepository;
use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;
use PDO;

class PostService
{
    private const DEFAULT_TIMEZONE = TIMEZONE;

    public function __construct(
        private PostRepository $posts,
        private TopicRepository $topics,
        private string $timezone = self::DEFAULT_TIMEZONE
    ) {
    }

    public static function build(PDO $pdo, string $topicsPath): self
    {
        return new self(
            new PostRepository($pdo),
            new TopicRepository($pdo, $topicsPath)
        );
    }

    public function ensureTopicsSeeded(): void
    {
        $this->topics->seedFromFileIfEmpty();
    }

    public function getLatestPosts(int $limit = 20): array
    {
        return $this->posts->latest($limit);
    }

    public function getPostBySlug(string $slug): ?array
    {
        return $this->posts->findBySlug($slug);
    }

    public function sanitizeContent(string $html): string
    {
        $allowedTags = [
            'p', 'h2', 'h3', 'ul', 'ol', 'li', 'strong', 'em', 'br', 'a', 'span', 'div', 'section', 'figure', 'figcaption',
        ];

        $allowedAttributes = [
            '*' => ['class'],
            'a' => ['href', 'title', 'target', 'rel', 'class'],
        ];

        $allowedSchemes = ['http', 'https', 'mailto', ''];

        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $container = $dom->documentElement;

        $sanitizeNode = function (DOMNode $node) use (&$sanitizeNode, $allowedTags, $allowedAttributes, $allowedSchemes): void {
            foreach (iterator_to_array($node->childNodes) as $child) {
                if ($child instanceof DOMComment) {
                    $node->removeChild($child);
                    continue;
                }

                if ($child instanceof DOMElement) {
                    $tag = strtolower($child->tagName);
                    if (!in_array($tag, $allowedTags, true)) {
                        while ($child->hasChildNodes()) {
                            $node->insertBefore($child->firstChild, $child);
                        }
                        $node->removeChild($child);
                        continue;
                    }

                    $allowed = array_unique(array_merge($allowedAttributes['*'] ?? [], $allowedAttributes[$tag] ?? []));
                    foreach (iterator_to_array($child->attributes) as $attribute) {
                        if (!in_array($attribute->name, $allowed, true)) {
                            $child->removeAttributeNode($attribute);
                        }
                    }

                    if ($child->hasAttribute('href')) {
                        $href = trim($child->getAttribute('href'));
                        $scheme = strtolower((string) parse_url($href, PHP_URL_SCHEME));
                        if ($scheme !== '' && !in_array($scheme, $allowedSchemes, true)) {
                            $child->removeAttribute('href');
                        }

                        if (stripos($href, 'javascript:') === 0) {
                            $child->removeAttribute('href');
                        }
                    }

                    $sanitizeNode($child);
                }
            }
        };

        $sanitizeNode($container);

        $cleanHtml = '';
        foreach ($container->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        return $cleanHtml;
    }

    public function generatePostFromNextTopic(): ?array
    {
        $topic = $this->topics->nextUnused();
        if (!$topic) {
            return null;
        }

        $post = $this->createPostFromTopic($topic);
        $this->topics->markAsUsed((int) $topic['id']);

        return $post;
    }

    private function createPostFromTopic(array $topic): array
    {
        $payload = $this->generateContent($topic);
        $slug = $this->generateUniqueSlug($topic['title']);

        return $this->posts->create([
            'slug' => $slug,
            'title' => $topic['title'],
            'summary' => $payload['summary'],
            'content' => $payload['content'],
            'topic_title' => $topic['title'],
            'topic_description' => $topic['description'],
        ], new \DateTimeZone($this->timezone));
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = $this->slugify($title);
        $base = $slug;
        $suffix = 1;

        while ($this->posts->slugExists($slug)) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function generateContent(array $topic): array
    {
        $title = $topic['title'];
        $description = $topic['description'];

        $intro = sprintf(
            'You are not overreacting—%s is exhausting, especially when every text or pickup feels like a trap. This guide validates that strain and then moves quickly into repeatable actions you can take to protect your time, your kids, and your case.',
            htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
        );

        $sections = [
            'Quick orientation' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . ' We start with a north star: calm responses, documentation that would make sense to a judge, and boundaries that do not depend on your co-parent agreeing with you.',
            'Grey rock without disengaging from parenting' => 'Use BIFF (brief, informative, friendly, firm) replies. Example: “I’m following the parenting plan. If you want a change, please send the request in writing.” Keep tone neutral, no extra justification. If they escalate, do not match their energy—repeat the boundary and move on.',
            'Parallel parenting structure' => 'Document exchange times, pickup locations, and handoff notes in one consistent place. Share only essential info about the child (health, schooling, safety). When they push for extra access or last-minute swaps, respond with: “I will follow the schedule we both signed. If you want to propose a change, I can review it in writing.”',
            'Documentation that holds up' => 'Keep an incident log: date, time, channel (text/email), short facts, impact on the child, and whether you replied. Avoid adjectives—stick to observable details. Screenshot important messages and back them up weekly. If something is urgent (safety, threats), note whether you contacted counsel or authorities.',
            'Protecting kids from crossfire' => 'Do not send messages through the child. Script for a child who reports conflict: “Thank you for telling me. This is adult stuff and I will handle it.” Reassure routines: meals, bedtime, school drop-offs stay predictable. Add one calming ritual at transitions—a five-minute walk, a shared playlist, or breathing together in the car.',
            'Emotional regulation for you' => 'Before replying, pause: sip water, breathe out for twice as long as you breathe in, and set a 10-minute timer. Write a vent draft in notes, then delete. Send only the BIFF version. Name your triggers (mocking, accusations, deadline pressure) so you can plan scripts in advance.',
            'When court is looming' => 'Assume everything you write will be printed. Avoid sarcasm, threats, or diagnosing the other parent. If they refuse to follow the order, document the refusal and offer two compliant options. Example: “Per the order, pickup is 5:30 at school. If you prefer the library lobby, confirm by noon.”',
            'Support network' => 'Line up allies who can speak to your steadiness: teachers, pediatricians, therapists, neutral third-party supervisors. Keep them updated with short, factual summaries instead of rants. If finances allow, consult a family law attorney or mediator familiar with high-conflict dynamics for jurisdiction-specific guidance.',
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

        $books = $this->selectAffiliateBooks($title);

        $contentParts = [
            ['type' => 'paragraph', 'text' => $intro],
        ];

        foreach ($sections as $heading => $body) {
            $contentParts[] = ['type' => 'heading', 'text' => $heading];
            $contentParts[] = ['type' => 'paragraph', 'text' => $body];
        }

        $contentParts[] = [
            'type' => 'list',
            'heading' => 'Action steps you can start today',
            'items' => array_map('htmlspecialchars', $actionSteps),
            'class' => 'action-list',
        ];

        $contentParts[] = [
            'type' => 'list',
            'heading' => 'Scripts to copy and paste',
            'items' => array_map('htmlspecialchars', $scripts),
            'class' => 'script-list',
        ];

        $contentParts[] = [
            'type' => 'paragraph',
            'text' => '<strong>Date/Time:</strong> ____ | <strong>Channel:</strong> text/email | <strong>Facts:</strong> short, neutral statement | <strong>Impact on child:</strong> tired/late/overstimulated | <strong>Reply sent:</strong> yes/no + BIFF summary.',
            'heading' => 'Documentation template',
        ];

        $contentParts[] = [
            'type' => 'affiliate_books',
            'heading' => 'Recommended books',
            'items' => $books,
            'note' => 'These are affiliate links—we may earn a small commission. They are chosen because they match the needs of high-conflict, parallel parenting cases.',
        ];

        $contentParts[] = [
            'type' => 'paragraph',
            'text' => 'Disclaimer: This article is informational and not legal advice or therapy. Consult local counsel and mental health professionals for guidance specific to your family.',
            'class' => 'disclaimer',
        ];

        $content = $this->renderContent($contentParts);
        $content = $this->padContentToWordCount($content, 800);

        return [
            'summary' => $this->buildSummary($content),
            'content' => $content,
        ];
    }

    private function renderContent(array $parts): string
    {
        $html = '';

        foreach ($parts as $part) {
            switch ($part['type']) {
                case 'heading':
                    $html .= '<h2>' . $part['text'] . '</h2>';
                    break;
                case 'list':
                    $html .= '<h2>' . $part['heading'] . '</h2><ul class="' . ($part['class'] ?? '') . '"><li>' . implode('</li><li>', $part['items']) . '</li></ul>';
                    break;
                case 'affiliate_books':
                    $items = array_map(function ($book) {
                        return '<li><a rel="sponsored" target="_blank" href="' . $this->buildAffiliateLink($book['asin']) . '">' . htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') . '</a><span class="book-note"> — ' . htmlspecialchars($book['focus'], ENT_QUOTES, 'UTF-8') . '</span></li>';
                    }, $part['items']);

                    $html .= '<h2>' . $part['heading'] . '</h2><p class="affiliate-note">' . htmlspecialchars($part['note'], ENT_QUOTES, 'UTF-8') . '</p><ul class="book-list">' . implode('', $items) . '</ul>';
                    break;
                case 'paragraph':
                default:
                    if (!empty($part['heading'])) {
                        $html .= '<h2>' . $part['heading'] . '</h2>';
                    }
                    $html .= '<p' . (!empty($part['class']) ? ' class="' . $part['class'] . '"' : '') . '>' . $part['text'] . '</p>';
                    break;
            }
        }

        return $html;
    }

    private function padContentToWordCount(string $content, int $minimumWords): string
    {
        $wordCount = str_word_count(strip_tags($content));
        $fillerParagraphs = [
            '<p>Progress in high-conflict cases is measured in steadiness, not the other parent changing. Each calm, documented response builds credibility with the court and gives your child a predictable anchor in the chaos.</p>',
            '<p>Judges and evaluators look for consistent, child-focused behavior over time. Documenting patterns and answering only what is necessary shows you are prioritizing stability over drama.</p>',
            '<p>Parallel parenting is not giving up; it is choosing routines and communication structures that protect your child when cooperation is impossible.</p>',
            '<p>Short, timely responses paired with clear records reduce the chances of misquoting and keep the focus on the parenting plan instead of personal attacks.</p>',
            '<p>Even small rituals—packing the same snack for exchanges, confirming times in one place—signal predictability to your child and lower their stress.</p>',
            '<p>Set reminders to review your log weekly so you catch gaps, note improvements, and adjust scripts before the next difficult exchange.</p>',
        ];

        $index = 0;
        $lastParagraph = null;

        while ($wordCount < $minimumWords) {
            $paragraph = $fillerParagraphs[$index % count($fillerParagraphs)];

            if ($paragraph === $lastParagraph && count($fillerParagraphs) > 1) {
                $index++;
                $paragraph = $fillerParagraphs[$index % count($fillerParagraphs)];
            }

            $content .= "\n\n" . $paragraph;
            $wordCount = str_word_count(strip_tags($content));
            $lastParagraph = $paragraph;
            $index++;
        }

        return $content;
    }

    private function buildSummary(string $content): string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($content)));
        if (mb_strlen($plain) <= 240) {
            return $plain;
        }

        $snippet = mb_substr($plain, 0, 240);
        $snippet = preg_replace('/\s+\S*$/u', '', $snippet);

        return rtrim($snippet) . '…';
    }

    private function selectAffiliateBooks(string $topicTitle): array
    {
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
            $score += strlen($book['title']) % 3;
            $book['score'] = $score;

            return $book;
        }, $catalog);

        usort($scored, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($scored, 0, 3);
    }

    private function buildAffiliateLink(string $asin): string
    {
        $tag = defined('AMAZON_AFFILIATE_ID') ? AMAZON_AFFILIATE_ID : '';

        return 'https://www.amazon.com/dp/' . rawurlencode($asin) . '?tag=' . urlencode($tag);
    }

    private function slugify(string $text): string
    {
        $text = preg_replace('~[\p{Pd}\s]+~u', '-', $text);
        $text = preg_replace('~[^\pL\pN-]+~u', '', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        if ($text === '') {
            $text = 'post-' . time();
        }

        return $text;
    }
}
