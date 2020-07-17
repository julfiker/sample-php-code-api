<?php

namespace App\Models\Eloquent\Hotspot;

use Illuminate\Database\Eloquent\Model;

class HotspotCategory extends Model
{
    protected $table = 'hotspot_category';

    public function hotspots()
    {
        return $this->belongsToMany(Hotspot::class);
    }
}
