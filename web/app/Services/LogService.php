<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogService
{
    public function createLog($entityType, $entityId, $type, $message, $userId = null)
    {
        return Log::create([
            'user_id' => $userId ?? Auth::id(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'type' => $type,
            'message' => $message,
        ]);
    }
}