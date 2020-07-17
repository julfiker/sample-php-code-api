<?php

namespace App\Models\Eloquent\Lists;

use App\Models\Eloquent\BaseModel;
use App\Models\Eloquent\User\User;
use Illuminate\Database\Eloquent\Model;

class Nationality extends BaseModel
{

    protected $table = 'nationality';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

}
