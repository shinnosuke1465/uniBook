<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\DealMessage
 *
 * @property string $id
 * @property string $user_id
 * @property string $deal_room_id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read DealRoom $dealRoom
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage whereDealRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DealMessage extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'deal_room_id' => 'string',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'deal_room_id',
        'message',
    ];

    /**
     * メッセージを送信したユーザーとの関係
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * メッセージが属する取引ルームとの関係
     */
    public function dealRoom(): BelongsTo
    {
        return $this->belongsTo(DealRoom::class);
    }
}