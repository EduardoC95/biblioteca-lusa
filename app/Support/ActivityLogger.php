<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogger
{
    public static function log(
        ?int $userId,
        string $module,
        ?int $objectId,
        string $action,
        ?string $description = null,
        ?Request $request = null
    ): void {
        ActivityLog::create([
            'user_id' => $userId,
            'module' => $module,
            'object_id' => $objectId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
