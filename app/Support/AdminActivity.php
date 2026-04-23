<?php

namespace App\Support;

use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class AdminActivity
{
    public static function log(
        string $eventType,
        string $description,
        ?int $causerId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $properties = []
    ): void {
        $request = request();

        AdminActivityLog::query()->create([
            'causer_id' => $causerId,
            'event_type' => $eventType,
            'description' => $description,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'route_name' => self::routeName($request),
            'url' => self::url($request),
            'method' => self::method($request),
            'ip_address' => self::ip($request),
            'user_agent' => self::userAgent($request),
            'properties' => empty($properties) ? null : $properties,
            'created_at' => now(),
        ]);
    }

    private static function routeName(mixed $request): ?string
    {
        return ($request instanceof Request) ? optional($request->route())->getName() : null;
    }

    private static function url(mixed $request): ?string
    {
        return ($request instanceof Request) ? $request->fullUrl() : null;
    }

    private static function method(mixed $request): ?string
    {
        return ($request instanceof Request) ? $request->method() : null;
    }

    private static function ip(mixed $request): ?string
    {
        return ($request instanceof Request) ? $request->ip() : null;
    }

    private static function userAgent(mixed $request): ?string
    {
        return ($request instanceof Request) ? $request->userAgent() : null;
    }
}
