<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    public const TYPE_LOGIN = 'login';
    public const TYPE_LOGOUT = 'logout';
    public const TYPE_NAVIGATION = 'navigation';
    public const TYPE_MODEL_CREATED = 'model_created';
    public const TYPE_MODEL_UPDATED = 'model_updated';
    public const TYPE_MODEL_DELETED = 'model_deleted';

    public $timestamps = false;

    protected $fillable = [
        'causer_id',
        'event_type',
        'description',
        'subject_type',
        'subject_id',
        'route_name',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'properties',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
