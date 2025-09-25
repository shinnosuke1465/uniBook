<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Deal;

/**
 * App\Models\DealEvent
 *
 * @property string $id
 * @property string $user_id
 * @property string $deal_id
 * @property string $actor_type
 * @property string $event_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User $user
 * @property-read Deal $deal
 */
class DealEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'deal_id' => 'string',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'deal_id',
        'actor_type',
        'event_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
