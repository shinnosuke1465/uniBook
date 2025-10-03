<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Image
 *
 * @property string $id
 * @property string $path
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'path',
        'type',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function textbookImages(): HasMany
    {
        return $this->hasMany(TextbookImage::class, 'image_id', 'id');
    }

    /**
     * 画像のフルパスを取得
     * storage/app/publicに保存された画像のURLを返す
     *
     * @return string|null
     */
    public function getImagePath(): ?string
    {
        if (empty($this->path)) {
            return null;
        }

        // storage/app/public 配下のパスからURLを生成
        // 例: path が "textbooks/image.jpg" の場合
        // http://localhost/storage/textbooks/image.jpg を返す
        return asset('storage/' . $this->path);
    }
}
