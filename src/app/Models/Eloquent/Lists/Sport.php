<?php

namespace App\Models\Eloquent\Lists;

use App\Models\Eloquent\Activity\Activity;
use App\Models\Eloquent\BaseModel;
use App\Models\Eloquent\Hotspot\HotspotCategory;
use App\Models\Eloquent\User\User;

class Sport extends BaseModel
{

    protected $table = 'sport';

    protected $fillable = ['name', 'user_id'];


    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function activities()
    {
        return $this->belongToMany(Activity::class);
    }

    public function category()
    {
        return $this->hasOne(HotspotCategory::class);
    }

}
