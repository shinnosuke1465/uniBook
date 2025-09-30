<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\DealRoomUser
 *
 * @property int $id
 * @property string $user_id
 * @property string $deal_room_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read DealRoom $dealRoom
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser whereDealRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoomUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DealRoomUser extends Model
{
    protected $table = 'deal_room_users';

    protected $casts = [
        'user_id' => 'string',
        'deal_room_id' => 'string',
    ];

    protected $fillable = [
        'user_id',
        'deal_room_id',
    ];

    /**
     * ユーザーとの関係
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 取引ルームとの関係
     */
    public function dealRoom(): BelongsTo
    {
        return $this->belongsTo(DealRoom::class, 'deal_room_id', 'id');
    }
}