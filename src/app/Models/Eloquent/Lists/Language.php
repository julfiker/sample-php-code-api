<?php

namespace App\Models\Eloquent\Lists;

use App\Models\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Language extends BaseModel
{

    protected $table = 'language';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
