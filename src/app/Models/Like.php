<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Textbook;

/**
 * App\Models\Like
 *
 * @property string $id
 * @property string $user_id
 * @property string $textbook_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User $user
 * @property-read Textbook $textbook
 */
class Like extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'textbook_id' => 'string',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'textbook_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function textbook(): BelongsTo
    {
        return $this->belongsTo(Textbook::class);
    }
}