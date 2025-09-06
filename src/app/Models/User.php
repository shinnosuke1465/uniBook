<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string $name
 * @property string $password
 * @property string $mail_address
 * @property string $post_code
 * @property string $address
 * @property string|null $image_id
 * @property string|null $university_id
 * @property string|null $faculty_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Faculty|null $faculty
 * @property-read \App\Models\Image|null $image
 * @property-read \App\Models\University|null $university
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFacultyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePostCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUniversityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class User extends Authenticatable
{

    use  HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'name',
        'password',
        'mail_address',
        'post_code',
        'address',
        'image_id',
        'university_id',
        'faculty_id',
    ];

    protected $hidden = [
        'password',
    ];


    public function university(): belongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function faculty(): belongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function image(): belongsTo
    {
        return $this->belongsTo(Image::class);
    }
}
