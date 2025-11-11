<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Log user activity
     */
    protected function logActivity($action, $description, $model = null, $modelId = null, $properties = null)
    {
        ActivityLog::log($action, $description, $model, $modelId, $properties);
    }
}
