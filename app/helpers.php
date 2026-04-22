<?php

use App\Support\CmsSecurity;

if (! function_exists('cms_sanitize_string')) {
    function cms_sanitize_string(?string $value, array $allowedTags = []): ?string
    {
        return CmsSecurity::sanitizeString($value, $allowedTags);
    }
}

if (! function_exists('cms_sanitize_array')) {
    function cms_sanitize_array(mixed $value, array $allowedTags = []): mixed
    {
        return CmsSecurity::sanitizeArray($value, $allowedTags);
    }
}

if (! function_exists('cms_plain_text')) {
    function cms_plain_text(?string $value): ?string
    {
        return CmsSecurity::plainText($value);
    }
}

if (! function_exists('cms_rich_text')) {
    function cms_rich_text(?string $value): ?string
    {
        return CmsSecurity::richText($value);
    }
}

if (! function_exists('cms_email')) {
    function cms_email(?string $value): ?string
    {
        return CmsSecurity::normalizeEmail($value);
    }
}

if (! function_exists('cms_phone')) {
    function cms_phone(?string $value): ?string
    {
        return CmsSecurity::normalizePhone($value);
    }
}

if (! function_exists('cms_ip_fingerprint')) {
    function cms_ip_fingerprint(?string $value): ?string
    {
        return CmsSecurity::ipFingerprint($value);
    }
}

if (! function_exists('cms_media_url')) {
    function cms_media_url(?string $path): ?string
    {
        $path = trim((string) $path);

        return $path === '' ? null : route('media.show', ['path' => $path]);
    }
}
