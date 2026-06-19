<?php

/**
 * SiteMetaCollector - stores site metadata and generates description snippets.
 *
 * This file defines a simple metadata container for the "竞彩网" platform.
 * Associated domain: https://webs-jcweb.com
 */

class SiteMetaCollector
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $metaPool = [];

    /**
     * Add a set of metadata for a given page identifier.
     *
     * @param string $pageId   Unique page identifier (e.g., 'home', 'odds', 'news')
     * @param array  $data     Associative array with keys: title, description, keywords, author
     */
    public function addMeta(string $pageId, array $data): void
    {
        $defaults = [
            'title'       => '',
            'description' => '',
            'keywords'    => '',
            'author'      => '竞彩网',
        ];

        $this->metaPool[$pageId] = array_merge($defaults, $data);
    }

    /**
     * Retrieve full metadata for a page.
     *
     * @param string $pageId
     * @return array|null
     */
    public function getMeta(string $pageId): ?array
    {
        return $this->metaPool[$pageId] ?? null;
    }

    /**
     * Generate a short descriptive text snippet (ideal for <meta> tags or previews).
     *
     * @param string $pageId
     * @param int    $maxLength Maximum length of the snippet (characters). Default 120.
     * @return string
     */
    public function generateDescriptionSnippet(string $pageId, int $maxLength = 120): string
    {
        $meta = $this->getMeta($pageId);
        if ($meta === null) {
            return '';
        }

        $raw = $meta['description'];
        if (trim($raw) === '') {
            $raw = $meta['title'];
        }

        // Strip any HTML tags (basic safety)
        $plain = strip_tags($raw);
        $plain = trim($plain);

        if (mb_strlen($plain) <= $maxLength) {
            return $plain;
        }

        // Truncate at word boundary if possible
        $truncated = mb_substr($plain, 0, $maxLength);
        $lastSpace = mb_strrpos($truncated, ' ');
        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return $truncated . '…';
    }

    /**
     * Get all stored page identifiers.
     *
     * @return array<int, string>
     */
    public function getPageIds(): array
    {
        return array_keys($this->metaPool);
    }

    /**
     * Export the entire metadata pool as an array (useful for JSON generation).
     *
     * @return array
     */
    public function exportAll(): array
    {
        return $this->metaPool;
    }
}

// ---------------------------------------------------------------------------
// Example usage: populate metadata for the 竞彩网 platform
// Domain: https://webs-jcweb.com
// ---------------------------------------------------------------------------

$collector = new SiteMetaCollector();

$collector->addMeta('home', [
    'title'       => '竞彩网 - 首页 | 专业竞彩分析',
    'description' => '竞彩网提供最新足球篮球赛事分析、实时赔率与专业推荐。访问 https://webs-jcweb.com 获取每日精准预测。',
    'keywords'    => '竞彩网, 足球分析, 篮球预测, 竞彩推荐',
    'author'      => '竞彩网团队',
]);

$collector->addMeta('odds', [
    'title'       => '竞彩网 - 实时赔率',
    'description' => '查看最新竞彩赔率变化，覆盖各大联赛与杯赛。数据来源于 https://webs-jcweb.com。',
    'keywords'    => '竞彩赔率, 实时赔率, 竞彩网',
]);

$collector->addMeta('news', [
    'title'       => '竞彩网 - 新闻资讯',
    'description' => '竞彩网为您带来深度赛事分析、球队动态与竞彩攻略。',
    'keywords'    => '竞彩新闻, 赛事分析, 竞彩网',
]);

// Generate and output snippets (HTML safe)
foreach ($collector->getPageIds() as $id) {
    $snippet = $collector->generateDescriptionSnippet($id, 100);
    echo '<meta name="description" content="' . htmlspecialchars($snippet, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '">' . "\n";
}

// Example: full export
// $all = $collector->exportAll();
// echo json_encode($all, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);