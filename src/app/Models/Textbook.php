<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use App\Models\University;
use App\Models\Faculty;

/**
 * App\Models\Textbook
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property string $condition_type
 * @property string $university_id
 * @property string $faculty_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Faculty $faculty
 * @property-read Collection<int, TextbookImage> $imageIds
 * @property-read int|null $textbook_images_count
 * @property-read University $university
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook query()
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereConditionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereFacultyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereUniversityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Textbook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Textbook extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'textbooks';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        'condition_type',
        'university_id',
        'faculty_id',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function textbookImages(): BelongsToMany
    {
        return $this->hasMany(TextbookImage::class, 'textbook_id', 'id');
    }

}
