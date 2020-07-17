<?php

namespace App\Models\Eloquent\Lists;

use App\Models\Eloquent\BaseModel;
use App\Models\Eloquent\User\User;
use Illuminate\Database\Eloquent\Model;

class Brand extends BaseModel
{

    protected $table = 'brand';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
