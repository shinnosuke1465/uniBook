<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Textbook;
use App\Models\DealEvent;

/**
 * App\Models\Deal
 *
 * @property string $id
 * @property string $seller_id
 * @property string $buyer_id
 * @property string $textbook_id
 * @property string $deal_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User $seller
 * @property-read User $buyer
 * @property-read Textbook $textbook
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DealEvent[] $dealEvents
 */
class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'seller_id' => 'string',
        'buyer_id' => 'string',
        'textbook_id' => 'string',
    ];

    protected $fillable = [
        'id',
        'seller_id',
        'buyer_id',
        'textbook_id',
        'deal_status',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function textbook(): BelongsTo
    {
        return $this->belongsTo(Textbook::class);
    }

    public function dealEvents(): HasMany
    {
        return $this->hasMany(DealEvent::class);
    }
}
