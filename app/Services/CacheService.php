<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public const TTL_DASHBOARD = 300;      // 5 minutes
    public const TTL_REPORTS   = 600;      // 10 minutes
    public const TTL_LOOKUPS   = 3600;     // 1 hour
    public const TTL_ADMIN     = 120;      // 2 minutes

    public static function version(int $userId): int
    {
        return (int) Cache::get(self::versionKey($userId), 1);
    }

    public static function bust(int $userId): void
    {
        $current = self::version($userId);
        Cache::forever(self::versionKey($userId), $current + 1);
    }

    public static function key(int $userId, string $group, string ...$parts): string
    {
        $v = self::version($userId);
        $suffix = implode(':', $parts);

        return $suffix === ''
            ? "user:{$userId}:v{$v}:{$group}"
            : "user:{$userId}:v{$v}:{$group}:{$suffix}";
    }

    public static function remember(int $userId, string $group, int $ttl, callable $callback, string ...$parts)
    {
        return Cache::remember(self::key($userId, $group, ...$parts), $ttl, $callback);
    }

    public static function forgetGroup(int $userId, string $group, string ...$parts): void
    {
        Cache::forget(self::key($userId, $group, ...$parts));
    }

    public static function bustAdmin(): void
    {
        Cache::forever('admin:version', ((int) Cache::get('admin:version', 1)) + 1);
    }

    public static function adminKey(string $group, string ...$parts): string
    {
        $v = (int) Cache::get('admin:version', 1);
        $suffix = implode(':', $parts);

        return $suffix === ''
            ? "admin:v{$v}:{$group}"
            : "admin:v{$v}:{$group}:{$suffix}";
    }

    private static function versionKey(int $userId): string
    {
        return "user:{$userId}:version";
    }
}