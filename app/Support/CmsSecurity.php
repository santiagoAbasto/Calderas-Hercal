<?php

namespace App\Support;

class CmsSecurity
{
    /**
     * Tags permitidas para rich text del CMS.
     *
     * @var array<int, string>
     */
    protected const ALLOWED_RICH_TEXT_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li',
        'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'span',
    ];

    public static function sanitizeString(?string $value, array $allowedTags = []): ?string
    {
        if ($value === null) {
            return null;
        }

        $allowed = '';
        foreach ($allowedTags as $tag) {
            $allowed .= '<' . trim($tag, '<> ') . '>';
        }

        $clean = strip_tags($value, $allowed);
        $clean = preg_replace('/\s+on\w+="[^"]*"/i', '', $clean);
        $clean = preg_replace("/\s+on\w+='[^']*'/i", '', $clean);
        $clean = preg_replace('/javascript\s*:/i', '', $clean);

        return trim((string) $clean);
    }

    public static function sanitizeArray(mixed $value, array $allowedTags = []): mixed
    {
        if (is_array($value)) {
            $sanitized = [];

            foreach ($value as $key => $item) {
                $sanitized[$key] = self::sanitizeArray($item, $allowedTags);
            }

            return $sanitized;
        }

        if (is_string($value)) {
            return self::sanitizeString($value, $allowedTags);
        }

        return $value;
    }

    public static function plainText(?string $value): ?string
    {
        return self::sanitizeString($value, []);
    }

    public static function richText(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = trim($html);

        if ($html === '') {
            return '';
        }

        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|textarea|select|option|link|meta)[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|textarea|select|option|link|meta)[^>]*/?>#is', '', $html);

        $allowed = '<' . implode('><', self::ALLOWED_RICH_TEXT_TAGS) . '>';
        $html = strip_tags($html, $allowed);

        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)>/i', static function (array $matches): string {
            $tag = strtolower($matches[1]);
            $attrs = $matches[2] ?? '';

            if ($tag !== 'a') {
                return "<{$tag}>";
            }

            preg_match_all('/([a-z0-9_:-]+)\s*=\s*("|\')(.*?)\2/i', $attrs, $attrMatches, PREG_SET_ORDER);

            $safeAttrs = [];

            foreach ($attrMatches as $attrMatch) {
                $name = strtolower($attrMatch[1]);
                $value = trim($attrMatch[3]);

                if (str_starts_with($name, 'on') || str_starts_with($name, 'data-') || $name === 'style') {
                    continue;
                }

                if ($name === 'href') {
                    $href = self::sanitizeUrl($value, allowRelative: true);

                    if ($href !== null) {
                        $safeAttrs[] = 'href="' . e($href) . '"';
                    }

                    continue;
                }

                if ($name === 'target' && in_array($value, ['_blank', '_self'], true)) {
                    $safeAttrs[] = 'target="' . e($value) . '"';
                    continue;
                }

                if ($name === 'rel') {
                    $safeAttrs[] = 'rel="noopener noreferrer"';
                }
            }

            $hasBlankTarget = str_contains(implode(' ', $safeAttrs), 'target="_blank"');
            $hasRel = str_contains(implode(' ', $safeAttrs), 'rel=');

            if ($hasBlankTarget && ! $hasRel) {
                $safeAttrs[] = 'rel="noopener noreferrer"';
            }

            return '<a' . (empty($safeAttrs) ? '' : ' ' . implode(' ', $safeAttrs)) . '>';
        }, $html);

        $html = preg_replace('/(<[^>]+?)  +/s', '$1 ', $html);

        return trim((string) $html);
    }

    public static function normalizeEmail(?string $email): ?string
    {
        $email = trim((string) $email);

        return $email === '' ? null : strtolower($email);
    }

    public static function normalizePhone(?string $phone): ?string
    {
        $phone = preg_replace('/[^\d\+\-\(\)\s]/', '', trim((string) $phone));
        $phone = preg_replace('/\s+/', ' ', (string) $phone);
        $phone = trim((string) $phone);

        return $phone === '' ? null : $phone;
    }

    public static function ipFingerprint(?string $ip): ?string
    {
        $ip = trim((string) $ip);

        if ($ip === '') {
            return null;
        }

        return hash_hmac('sha256', $ip, (string) config('app.key', 'calderas-hercal'));
    }

    public static function sanitizeUrl(?string $url, bool $allowRelative = false): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if ($allowRelative && preg_match('~^(?:/|#)~', $url)) {
            return $url;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true) ? $url : null;
    }
}
