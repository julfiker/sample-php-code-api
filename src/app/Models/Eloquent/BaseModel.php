<?php
/**
 * Created by PhpStorm.
 * User: saroj
 * Date: 9/14/15
 * Time: 2:35 PM
 */

namespace App\Models\Eloquent;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->toATOMString();
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->toATOMString();
    }

    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = Carbon::parse($value)->toDateTimeString();
    }
}