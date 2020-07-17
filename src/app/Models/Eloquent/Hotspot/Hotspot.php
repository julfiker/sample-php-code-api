<?php

namespace App\Models\Eloquent\Hotspot;

use App\Models\Eloquent\User\User;
use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $table = 'hotspot';
    protected $fillable = [
        'name',
        'category_id',
        'lat',
        'long',
        'street',
        'street_number',
        'address',
        'city',
        'country',
        'country_code',
        'user_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'category_name',
        'owner'
    ];

    public function category()
    {
        return $this->belongsTo(HotspotCategory::class);
    }

    public function owner()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    public function getCategoryNameAttribute()
    {
        return $this->category()->first()->name;
    }

    public function getOwnerAttribute($value)
    {
        return ($this->owner()->exists())? $this->owner()->first()->fullname : $value;
    }
}
