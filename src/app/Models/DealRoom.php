<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\DealRoom
 *
 * @property string $id
 * @property string $deal_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Deal $deal
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom newQuery()
 * @method static \Illuminate\Database\Query\Builder|DealRoom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom whereDealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DealRoom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|DealRoom withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DealRoom withoutTrashed()
 * @mixin \Eloquent
 */
class DealRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'deal_id' => 'string',
    ];

    protected $fillable = [
        'id',
        'deal_id',
    ];

    /**
     * 取引との関係
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    /**
     * 取引ルームに参加しているユーザーとの関係
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'deal_room_users', 'deal_room_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * ユーザーIDの配列を取得
     */
    public function getUserIds(): array
    {
        return $this->users()->pluck('users.id')->toArray();
    }
}