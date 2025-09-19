<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Textbook;
use App\Models\Image;

/**
 * App\Models\TextbookImage
 *
 * @property int $id
 * @property string $textbook_id
 * @property string $image_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Textbook $textbook
 * @property-read Image $image
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage whereTextbookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TextbookImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TextbookImage extends Model
{
    protected $table = 'textbook_images';

    protected $fillable = [
        'textbook_id',
        'image_id',
    ];

    public function textbook(): BelongsTo
    {
        return $this->belongsTo(Textbook::class, 'textbook_id', 'id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id', 'id');
    }
}
