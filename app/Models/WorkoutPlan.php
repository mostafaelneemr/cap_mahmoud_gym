<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkoutPlan extends GlobalModel
{
    use SoftDeletes, Notifiable, LogsActivity;

    protected $table = 'workout_plans';
    public $timestamps = true;
    public $primaryKey = 'id';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'warmup_links' => 'array',
    ];

    protected $fillable = ['trainee_id', 'day_name', 'warmup', 'warmup_links', 'post_workout', 'status'];

    /** Maximum number of warmup video links per workout day. */
    public const MAX_WARMUP_LINKS = 5;

    /** Supported content builder item types. */
    public const CONTENT_TYPE_TEXT = 'text';
    public const CONTENT_TYPE_ACTIVITY = 'activity';
    public const CONTENT_TYPE_VIDEO = 'video';

    /**
     * Sanitize rich-text HTML: allows safe formatting tags only,
     * strips dangerous tags/attributes, and ensures safe links.
     * Returns null when effectively empty.
     */
    public static function sanitizeRichText(?string $html): ?string
    {
        if (is_null($html) || trim($html) === '') {
            return null;
        }

        $trimmed = trim($html);

        // Strip scripts and styles completely
        $cleaned = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $trimmed);

        // Strip QuillTableBetter temporary elements completely so they do not inject rogue <br>
        $cleaned = preg_replace('/<temporary\b[^>]*>.*?<\/temporary>/is', '', $cleaned);

        // If it's a JSON array string from legacy content builder, convert it to HTML first
        if (($cleaned !== '' && ($cleaned[0] === '[' && substr($cleaned, -1) === ']') || ($cleaned[0] === '{' && substr($cleaned, -1) === '}'))) {
            $items = self::cleanContentItems($cleaned);
            if (!is_null($items)) {
                return self::renderItemsToHtml($items);
            }
        }

        // Allowed rich text tags for the editor including table hierarchy
        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><br><b><strong><i><em><u><s><ul><ol><li><a><blockquote><pre><code><hr><table><caption><colgroup><col><thead><tbody><tfoot><tr><th><td><span>';
        $cleaned = strip_tags($cleaned, $allowedTags);

        // Check if effectively empty (no real text content and no tables or structural elements)
        $plainText = trim(strip_tags($cleaned));
        if ($plainText === '' && !preg_match('/<(table|th|td|col|img|hr)/i', $cleaned)) {
            return null;
        }

        // Sanitize attributes: only allow safe attributes and block scripts/event handlers/unsafe URLs
        $cleaned = preg_replace_callback('/<([a-z0-9]+)([^>]*)>/i', function ($m) {
            $tag = strtolower($m[1]);
            $attrs = $m[2];

            // 1. Safe links
            if ($tag === 'a') {
                if (preg_match('/href=([\'"])(.*?)\1/i', $attrs, $hrefMatch)) {
                    $url = trim($hrefMatch[2]);
                    if (preg_match('~^(https?://|mailto:|tel:)~i', $url)) {
                        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
                        return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer">';
                    }
                }
                return '<a>';
            }

            // 2. Table tags and safe formatting elements
            if (in_array($tag, ['table', 'colgroup', 'col', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'p', 'span'], true)) {
                $allowedAttrs = [];

                // colspan / rowspan / span (positive integers only)
                if (in_array($tag, ['th', 'td', 'col'], true)) {
                    if (preg_match('/\bcolspan=[\'"]?(\d+)[\'"]?/i', $attrs, $cm)) {
                        $val = (int)$cm[1];
                        if ($val > 1) {
                            $allowedAttrs[] = 'colspan="' . $val . '"';
                        }
                    }
                    if (preg_match('/\browspan=[\'"]?(\d+)[\'"]?/i', $attrs, $rm)) {
                        $val = (int)$rm[1];
                        if ($val > 1) {
                            $allowedAttrs[] = 'rowspan="' . $val . '"';
                        }
                    }
                    if (preg_match('/\bspan=[\'"]?(\d+)[\'"]?/i', $attrs, $sm)) {
                        $val = (int)$sm[1];
                        if ($val > 1) {
                            $allowedAttrs[] = 'span="' . $val . '"';
                        }
                    }
                }

                // Quill table data attributes: data-row, data-cell (alphanumeric/hyphen/underscore)
                if (preg_match('/\bdata-row=[\'"]([a-zA-Z0-9_\-]+)[\'"]?/i', $attrs, $dm)) {
                    $allowedAttrs[] = 'data-row="' . htmlspecialchars($dm[1], ENT_QUOTES, 'UTF-8') . '"';
                }
                if (preg_match('/\bdata-cell=[\'"]([a-zA-Z0-9_\-]+)[\'"]?/i', $attrs, $dm)) {
                    $allowedAttrs[] = 'data-cell="' . htmlspecialchars($dm[1], ENT_QUOTES, 'UTF-8') . '"';
                }

                // Safe class attribute
                if (preg_match('/\bclass=[\'"]([a-zA-Z0-9_\-\s]+)[\'"]?/i', $attrs, $cm)) {
                    $classes = array_filter(explode(' ', trim($cm[1])));
                    $safeClasses = array_filter($classes, function ($c) {
                        return preg_match('/^(ql-[a-z0-9\-]+|table|table-[a-z0-9\-]+|wb-[a-z0-9\-]+|text-[a-z0-9\-]+)$/i', $c);
                    });
                    if (!empty($safeClasses)) {
                        $allowedAttrs[] = 'class="' . htmlspecialchars(implode(' ', $safeClasses), ENT_QUOTES, 'UTF-8') . '"';
                    }
                }

                // Safe inline style (width, text-align, etc. Strictly block expression, javascript, url)
                if (preg_match('/\bstyle=[\'"]([^\'"]+)[\'"]?/i', $attrs, $sm)) {
                    $rawStyle = $sm[1];
                    if (!preg_match('/(expression|javascript|behavior|vbscript|include|import|<|>)/i', $rawStyle)) {
                        $declarations = explode(';', $rawStyle);
                        $safeDeclarations = [];
                        foreach ($declarations as $decl) {
                            $parts = explode(':', trim($decl), 2);
                            if (count($parts) === 2) {
                                $prop = strtolower(trim($parts[0]));
                                $val = trim($parts[1]);
                                if (in_array($prop, ['width', 'max-width', 'min-width', 'text-align', 'vertical-align', 'border-color'], true)) {
                                    if (preg_match('/^[a-zA-Z0-9%\.\s\-]+$/', $val)) {
                                        $safeDeclarations[] = $prop . ': ' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                                    }
                                }
                            }
                        }
                        if (!empty($safeDeclarations)) {
                            $allowedAttrs[] = 'style="' . implode('; ', $safeDeclarations) . ';"';
                        }
                    }
                }

                $attrString = !empty($allowedAttrs) ? ' ' . implode(' ', $allowedAttrs) : '';
                return '<' . $tag . $attrString . '>';
            }

            return '<' . $tag . '>';
        }, $cleaned);

        $cleaned = trim($cleaned);
        return $cleaned !== '' ? $cleaned : null;
    }

    /**
     * Get HTML formatted content suitable for loading into the Quill rich-text editor on edit.
     * Converts legacy JSON content items, plain text, or raw HTML to safe HTML.
     */
    public function getWarmupForEditorAttribute(): string
    {
        return self::formatContentForEditor($this->warmup);
    }

    public function getPostWorkoutForEditorAttribute(): string
    {
        return self::formatContentForEditor($this->post_workout);
    }

    public static function formatContentForEditor(?string $raw): string
    {
        if (empty($raw) || trim($raw) === '') {
            return '';
        }

        // If JSON items from previous content builder
        $items = self::cleanContentItems($raw);
        if (!is_null($items)) {
            $html = '';
            foreach ($items as $item) {
                $type = $item['type'] ?? '';
                if ($type === self::CONTENT_TYPE_TEXT) {
                    $html .= '<p>' . ($item['text'] ?? '') . '</p>';
                } elseif ($type === self::CONTENT_TYPE_ACTIVITY) {
                    $parts = [];
                    if (!empty($item['sets'])) $parts[] = __('Sets') . ': ' . e($item['sets']);
                    if (!empty($item['reps'])) $parts[] = __('Reps') . ': ' . e($item['reps']);
                    if (!empty($item['duration'])) $parts[] = e($item['duration']);
                    if (!empty($item['rest'])) $parts[] = __('Rest') . ': ' . e($item['rest']);
                    $meta = !empty($parts) ? ' (' . implode(', ', $parts) . ')' : '';
                    $html .= '<p><strong>' . e($item['name'] ?? '') . '</strong>' . $meta . '</p>';
                } elseif ($type === self::CONTENT_TYPE_VIDEO) {
                    $title = !empty($item['title']) ? e($item['title']) : __('Video Link');
                    $url = e($item['url'] ?? '');
                    $html .= '<p><a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $title . '</a></p>';
                }
            }
            return self::sanitizeRichText($html) ?? '';
        }

        // If legacy plain text (no HTML tags)
        if (strip_tags($raw) === $raw) {
            $text = nl2br(htmlspecialchars($raw, ENT_QUOTES, 'UTF-8'));
            $text = preg_replace('~(https?://[^\s<]+)~i', '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);
            return '<p>' . $text . '</p>';
        }

        return self::sanitizeRichText($raw) ?? '';
    }

    /**
     * Normalize, validate, and clean an array or JSON string of content items.
     * Guarantees safe schema, strict types, and ordered list.
     */
    public static function cleanContentItems(mixed $value): ?array
    {
        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '' || $trimmed === 'null') {
                return null;
            }
            $decoded = json_decode($trimmed, true);
            if (!is_array($decoded)) {
                return null;
            }
            $value = $decoded;
        }

        if (!is_array($value)) {
            return null;
        }

        // Sort by 'order' attribute if provided, preserving array keys if already sequential
        usort($value, function ($a, $b) {
            $orderA = is_array($a) && isset($a['order']) ? (int) $a['order'] : 999999;
            $orderB = is_array($b) && isset($b['order']) ? (int) $b['order'] : 999999;
            return $orderA <=> $orderB;
        });

        $cleanItems = [];
        $order = 1;

        foreach ($value as $rawItem) {
            if (!is_array($rawItem)) {
                continue;
            }

            $type = isset($rawItem['type']) ? trim((string) $rawItem['type']) : '';
            $id = !empty($rawItem['id']) ? (string) $rawItem['id'] : 'item_' . uniqid() . '_' . $order;

            if ($type === self::CONTENT_TYPE_TEXT) {
                $text = self::sanitizeRichText($rawItem['text'] ?? ($rawItem['content'] ?? ''));
                if ($text === '') {
                    continue;
                }
                $cleanItems[] = [
                    'id' => $id,
                    'type' => self::CONTENT_TYPE_TEXT,
                    'order' => $order++,
                    'text' => $text,
                ];
            } elseif ($type === self::CONTENT_TYPE_ACTIVITY) {
                $name = isset($rawItem['name']) ? trim((string) $rawItem['name']) : '';
                if ($name === '') {
                    continue;
                }
                $sets = isset($rawItem['sets']) && trim((string)$rawItem['sets']) !== '' ? mb_substr(trim((string)$rawItem['sets']), 0, 50) : null;
                $reps = isset($rawItem['reps']) && trim((string)$rawItem['reps']) !== '' ? mb_substr(trim((string)$rawItem['reps']), 0, 50) : null;
                $duration = isset($rawItem['duration']) && trim((string)$rawItem['duration']) !== '' ? mb_substr(trim((string)$rawItem['duration']), 0, 50) : null;
                $rest = isset($rawItem['rest']) && trim((string)$rawItem['rest']) !== '' ? mb_substr(trim((string)$rawItem['rest']), 0, 50) : null;

                $cleanItems[] = [
                    'id' => $id,
                    'type' => self::CONTENT_TYPE_ACTIVITY,
                    'order' => $order++,
                    'name' => mb_substr($name, 0, 150),
                    'sets' => $sets,
                    'reps' => $reps,
                    'duration' => $duration,
                    'rest' => $rest,
                ];
            } elseif ($type === self::CONTENT_TYPE_VIDEO) {
                $title = isset($rawItem['title']) ? trim((string) $rawItem['title']) : '';
                $url = isset($rawItem['url']) ? trim((string) $rawItem['url']) : '';

                if ($url === '') {
                    continue;
                }
                if (!preg_match('~^https?://~i', $url)) {
                    $url = 'https://' . $url;
                }
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    continue;
                }

                $cleanItems[] = [
                    'id' => $id,
                    'type' => self::CONTENT_TYPE_VIDEO,
                    'order' => $order++,
                    'title' => mb_substr($title ?: __('Video Link'), 0, 150),
                    'url' => mb_substr($url, 0, 255),
                ];
            }
        }

        return empty($cleanItems) ? null : $cleanItems;
    }

    /**
     * Encode content items into a clean JSON string, or null when empty.
     */
    public static function encodeContentItems(mixed $value): ?string
    {
        $items = self::cleanContentItems($value);
        if (is_null($items)) {
            return null;
        }
        return json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Helper to render structured items into semantic HTML for views.
     */
    public static function renderItemsToHtml(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<div class="cb-display-list d-flex flex-column gap-2">';
        foreach ($items as $item) {
            $type = $item['type'] ?? '';
            if ($type === self::CONTENT_TYPE_TEXT) {
                $html .= '<div class="cb-display-item cb-display-item--text p-3 rounded" style="background: rgba(30, 41, 59, 0.45); border: 1px solid rgba(51, 65, 85, 0.4);">';
                $html .= '<div class="d-flex align-items-center gap-2 mb-1 fs-7 fw-bold" style="color: #38BDF8;"><i class="fas fa-align-left"></i><span>' . __('Instructions') . '</span></div>';
                $html .= '<div class="cb-display-rich fs-7">' . $item['text'] . '</div>';
                $html .= '</div>';
            } elseif ($type === self::CONTENT_TYPE_ACTIVITY) {
                $html .= '<div class="cb-display-item cb-display-item--activity p-3 rounded d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: rgba(30, 41, 59, 0.45); border: 1px solid rgba(51, 65, 85, 0.4);">';
                $html .= '<div class="d-flex align-items-center gap-2">';
                $html .= '<span class="cb-display-icon d-inline-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: rgba(250, 204, 21, 0.15); color: #FACC15;"><i class="fas fa-running"></i></span>';
                $html .= '<span class="fw-semibold text-light">' . e($item['name']) . '</span>';
                $html .= '</div>';
                $html .= '<div class="d-flex flex-wrap align-items-center gap-2 fs-8">';
                if (!empty($item['sets'])) {
                    $html .= '<span class="badge" style="background: rgba(99, 102, 241, 0.2); color: #A5B4FC; border: 1px solid rgba(99, 102, 241, 0.3);">' . __('Sets') . ': ' . e($item['sets']) . '</span>';
                }
                if (!empty($item['reps'])) {
                    $html .= '<span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #6EE7B7; border: 1px solid rgba(16, 185, 129, 0.3);">' . __('Reps') . ': ' . e($item['reps']) . '</span>';
                }
                if (!empty($item['duration'])) {
                    $html .= '<span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.3);"><i class="fas fa-stopwatch me-1"></i>' . e($item['duration']) . '</span>';
                }
                if (!empty($item['rest'])) {
                    $html .= '<span class="badge" style="background: rgba(148, 163, 184, 0.2); color: #CBD5E1; border: 1px solid rgba(148, 163, 184, 0.3);">' . __('Rest') . ': ' . e($item['rest']) . '</span>';
                }
                $html .= '</div>';
                $html .= '</div>';
            } elseif ($type === self::CONTENT_TYPE_VIDEO) {
                $html .= '<div class="cb-display-item cb-display-item--video p-3 rounded d-flex align-items-center justify-content-between gap-2" style="background: rgba(30, 41, 59, 0.45); border: 1px solid rgba(51, 65, 85, 0.4);">';
                $html .= '<div class="d-flex align-items-center gap-2">';
                $html .= '<span class="cb-display-icon d-inline-flex align-items-center justify-content-center rounded" style="width: 28px; height: 28px; background: rgba(244, 63, 94, 0.15); color: #F43F5E;"><i class="fab fa-youtube"></i></span>';
                $html .= '<span class="fw-semibold text-light">' . e($item['title']) . '</span>';
                $html .= '</div>';
                $html .= '<a href="' . e($item['url']) . '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light-danger d-inline-flex align-items-center gap-1 py-1 px-3 fs-8">';
                $html .= '<i class="fas fa-external-link-alt fs-9"></i><span>' . __('Watch Video') . '</span>';
                $html .= '</a>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Retrieve Warmup items as structured array with automatic legacy fallback.
     */
    public function getWarmupItemsAttribute(): array
    {
        return $this->resolveSectionItems('warmup', 'warmupExercises', $this->warmup_links_list);
    }

    /**
     * Retrieve Post-workout items as structured array with automatic legacy fallback.
     */
    public function getPostWorkoutItemsAttribute(): array
    {
        return $this->resolveSectionItems('post_workout', 'postWorkoutExercises', []);
    }

    /**
     * Helper to resolve structured items or legacy content for a section.
     */
    protected function resolveSectionItems(string $column, string $relation, array $legacyLinks = []): array
    {
        $raw = $this->attributes[$column] ?? null;

        // 1. If column already stores valid JSON items
        if (!empty($raw) && is_string($raw)) {
            $items = self::cleanContentItems($raw);
            if (!is_null($items)) {
                return $items;
            }
        }

        // 2. Legacy fallback: construct items from legacy column text, links, and exercise relation
        $legacyItems = [];
        $order = 1;

        // A. Legacy text / instructions
        if (!empty($raw) && is_string($raw) && trim($raw) !== '') {
            $legacyItems[] = [
                'id' => 'legacy_' . $column . '_text',
                'type' => self::CONTENT_TYPE_TEXT,
                'order' => $order++,
                'text' => self::sanitizeRichText($raw),
            ];
        }

        // B. Legacy video links
        if (!empty($legacyLinks)) {
            foreach ($legacyLinks as $idx => $link) {
                if (is_scalar($link) && filter_var((string)$link, FILTER_VALIDATE_URL)) {
                    $legacyItems[] = [
                        'id' => 'legacy_' . $column . '_video_' . ($idx + 1),
                        'type' => self::CONTENT_TYPE_VIDEO,
                        'order' => $order++,
                        'title' => __('Video Link') . ' #' . ($idx + 1),
                        'url' => (string)$link,
                    ];
                }
            }
        }

        // C. Legacy exercise table records
        if ($this->relationLoaded($relation)) {
            $exercises = $this->$relation;
        } else {
            $type = ($column === 'warmup') ? 'warmup' : 'post_workout';
            $exercises = $this->exercises()->where('type', $type)->get();
        }

        if ($exercises && $exercises->isNotEmpty()) {
            foreach ($exercises as $ex) {
                $legacyItems[] = [
                    'id' => 'legacy_' . $column . '_ex_' . $ex->id,
                    'type' => self::CONTENT_TYPE_ACTIVITY,
                    'order' => $order++,
                    'name' => $ex->name,
                    'sets' => $ex->sets ? (string)$ex->sets : null,
                    'reps' => $ex->reps ? (string)$ex->reps : null,
                    'duration' => null,
                    'rest' => $ex->rest ? (string)$ex->rest : null,
                ];
                if (!empty($ex->link) && filter_var($ex->link, FILTER_VALIDATE_URL)) {
                    $legacyItems[] = [
                        'id' => 'legacy_' . $column . '_ex_vid_' . $ex->id,
                        'type' => self::CONTENT_TYPE_VIDEO,
                        'order' => $order++,
                        'title' => $ex->name . ' - ' . __('Video'),
                        'url' => $ex->link,
                    ];
                }
            }
        }

        return $legacyItems;
    }

    /**
     * Check if the section contains the structured Content Builder JSON format.
     */
    public function isContentBuilderFormat(string $column): bool
    {
        $raw = $this->attributes[$column] ?? null;
        if (empty($raw) || !is_string($raw)) {
            return false;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded);
    }

    /**
     * Normalize warmup links input: trimmed strings, no empty values,
     * at most MAX_WARMUP_LINKS items. Returns null when nothing is left
     * so the JSON column stays NULL instead of "[]".
     */
    public static function normalizeWarmupLinks(mixed $value): ?array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [$value];
        }

        if (!is_array($value)) {
            return null;
        }

        $links = array_filter(
            array_map(fn ($link) => is_scalar($link) ? trim((string) $link) : '', $value),
            fn ($link) => $link !== ''
        );

        $links = array_slice(array_values($links), 0, self::MAX_WARMUP_LINKS);

        return empty($links) ? null : $links;
    }

    /** Always returns a clean list of warmup links (never null). */
    public function getWarmupLinksListAttribute(): array
    {
        return self::normalizeWarmupLinks($this->warmup_links) ?? [];
    }

    public function getDayNameAttribute($value): ?string
    {
        if ($value === null) {
            return null;
        }
        return trim(str_replace('[INACTIVE]', '', (string)$value));
    }

    public function setDayNameAttribute($value): void
    {
        $clean = trim(str_replace('[INACTIVE]', '', (string)$value));
        $currentRaw = $this->attributes['day_name'] ?? '';
        if (str_starts_with((string)$currentRaw, '[INACTIVE]')) {
            $this->attributes['day_name'] = '[INACTIVE] ' . $clean;
        } else {
            $this->attributes['day_name'] = $clean;
        }
    }

    public function getStatusAttribute($value): string
    {
        if ($value === 'archived') {
            return 'archived';
        }
        $rawDayName = $this->attributes['day_name'] ?? '';
        if (str_starts_with((string)$rawDayName, '[INACTIVE]')) {
            return 'inactive';
        }
        return $value ?: 'active';
    }

    public function setStatusAttribute($value): void
    {
        if ($value === 'inactive') {
            $this->attributes['status'] = 'active';
            $rawDayName = $this->attributes['day_name'] ?? '';
            if (!str_starts_with((string)$rawDayName, '[INACTIVE]')) {
                $this->attributes['day_name'] = '[INACTIVE] ' . trim((string)$rawDayName);
            }
        } elseif ($value === 'active') {
            $this->attributes['status'] = 'active';
            $rawDayName = $this->attributes['day_name'] ?? '';
            $this->attributes['day_name'] = trim(str_replace('[INACTIVE]', '', (string)$rawDayName));
        } else {
            $this->attributes['status'] = $value;
        }
    }

    public function getDisplayDayNameAttribute(): ?string
    {
        return $this->day_name;
    }

    public function getDisplayWarmupAttribute(): ?string
    {
        if (empty($this->warmup)) {
            return null;
        }

        $items = self::cleanContentItems($this->warmup);
        if (!is_null($items)) {
            return self::renderItemsToHtml($items);
        }

        $content = (string) $this->warmup;

        // If legacy plain text (no HTML tags), safely escape and preserve line breaks with auto-linked URLs
        if (strip_tags($content) === $content) {
            $content = nl2br(e($content));
            $content = preg_replace(
                '~(https?://[^\s<]+)~i',
                '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                $content
            );
        } else {
            // Ensure any <a> tags in HTML have target="_blank" and rel="noopener noreferrer"
            $content = preg_replace_callback(
                '/<a\b([^>]*)>/i',
                function ($matches) {
                    $attrs = $matches[1];
                    if (!preg_match('/\btarget\s*=/i', $attrs)) {
                        $attrs .= ' target="_blank"';
                    }
                    if (!preg_match('/\brel\s*=/i', $attrs)) {
                        $attrs .= ' rel="noopener noreferrer"';
                    }
                    return '<a' . $attrs . '>';
                },
                $content
            );

            // Wrap tables in responsive wrapper if not already wrapped
            if (stripos($content, '<table') !== false && stripos($content, 'table-responsive') === false) {
                $content = preg_replace('/(<table\b[^>]*>.*?<\/table>)/is', '<div class="table-responsive my-2">$1</div>', $content);
            }
        }

        return $content;
    }

    public function getDisplayPostWorkoutAttribute(): ?string
    {
        if (empty($this->post_workout)) {
            return null;
        }

        $items = self::cleanContentItems($this->post_workout);
        if (!is_null($items)) {
            return self::renderItemsToHtml($items);
        }

        $content = (string) $this->post_workout;

        // If legacy plain text (no HTML tags), safely escape and preserve line breaks with auto-linked URLs
        if (strip_tags($content) === $content) {
            $content = nl2br(e($content));
            $content = preg_replace(
                '~(https?://[^\s<]+)~i',
                '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                $content
            );
        } else {
            // Ensure any <a> tags in HTML have target="_blank" and rel="noopener noreferrer"
            $content = preg_replace_callback(
                '/<a\b([^>]*)>/i',
                function ($matches) {
                    $attrs = $matches[1];
                    if (!preg_match('/\btarget\s*=/i', $attrs)) {
                        $attrs .= ' target="_blank"';
                    }
                    if (!preg_match('/\brel\s*=/i', $attrs)) {
                        $attrs .= ' rel="noopener noreferrer"';
                    }
                    return '<a' . $attrs . '>';
                },
                $content
            );

            // Wrap tables in responsive wrapper if not already wrapped
            if (stripos($content, '<table') !== false && stripos($content, 'table-responsive') === false) {
                $content = preg_replace('/(<table\b[^>]*>.*?<\/table>)/is', '<div class="table-responsive my-2">$1</div>', $content);
            }
        }

        return $content;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class, 'trainee_id', 'id');
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'workout_plan_id', 'id');
    }

    public function warmupExercises()
    {
        return $this->hasMany(Exercise::class, 'workout_plan_id', 'id')->where('type', 'warmup');
    }

    public function mainExercises()
    {
        return $this->hasMany(Exercise::class, 'workout_plan_id', 'id')->where('type', 'main');
    }

    public function postWorkoutExercises()
    {
        return $this->hasMany(Exercise::class, 'workout_plan_id', 'id')->where('type', 'post_workout');
    }

    /**
     * تقارير تعديل أوزان التمارين المرتبطة باليوم التدريبي
     */
    public function loadAdjustments()
    {
        return $this->hasMany(TraineeLoadAdjustment::class, 'workout_plan_id', 'id')->orderBy('created_at', 'desc');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($workoutPlan) {
            $exercises = $workoutPlan->exercises()->get();
            foreach ($exercises as $exercise) {
                $workoutPlan->isForceDeleting() ? $exercise->forceDelete() : $exercise->delete();
            }
        });

        static::restoring(function ($workoutPlan) {
            Exercise::onlyTrashed()->where('workout_plan_id', $workoutPlan->id)->get()->each->restore();
        });
    }
}
