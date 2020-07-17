<?php

namespace App\Models\Eloquent\Activity;

use App\Models\Eloquent\BaseModel;
use App\Models\Eloquent\Group\Group;
use App\Models\Eloquent\Hotspot\Hotspot;
use App\Models\Eloquent\Lists\Sport;
use App\Models\Eloquent\User\User;
use App\Models\Enum\ActivityInvitationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends BaseModel
{

    use SoftDeletes;

    protected $table = 'activity';

    protected $with = ['participants', 'sport', 'hotspot', 'group'];

    protected $fillable = [
        'title',
        'sport_id',
        'owner_id',
        'start_time',
        'end_time',
        'description',
        'recurring',
        'privacy',
        'max_participants',
        'lat',
        'long',
        'street',
        'street_number',
        'address',
        'city',
        'country',
        'country_code',
        'hotspot_id',
        'group_id'
    ];

    protected $data = ['deleted_at'];


    /**
     * The users that belong to the activity.
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'pivot_activity_user',
            'activity_id',
            'user_id'
        )
            ->withPivot('status')
            ->withTimestamps();
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function hotspot()
    {
        return $this->belongsTo(
            Hotspot::class,
            'hotspot_id',
            'id'
        );
    }

    public function group()
    {
        return $this->belongsTo(
            Group::class,
            'group_id',
            'id'
        );
    }

    /**
     * Before 28/12/2017 lat, long, address, city, country, country_code keep in activity table.
     * After  28/12/2017 we move all fields into hotspot table.
     * Cause we allow use create hotspot.
     * Then we use getLatAttribute for replase old field with new field that not effect to frontend.
     *
     * @return mixed
     */
    public function getLatAttribute($value)
    {
        return ($this->hotspot()->exists()) ? $this->hotspot()->first()->lat : $value;
    }

    public function getLongAttribute($value)
    {
        return ($this->hotspot()->exists())? $this->hotspot()->first()->long : $value;
    }

    public function getStreetAttribute($value)
    {
        return ($this->hotspot()->exists())? $this->hotspot()->first()->street : $value;
    }

    public function getStreetNumberAttribute($value)
    {
        return ($this->hotspot()->exists())? $this->hotspot()->first()->street_number : $value;
    }

    public function getAddressAttribute($value)
    {
        return ($this->hotspot()->exists())? $this->hotspot()->first()->address: $value;
    }

    public function getCityAttribute($value)
    {
        return ($this->hotspot()->exists())? $this->hotspot()->first()->city : $value;
    }

    public function getCountryAttribute($value)
    {
        return ($this->hotspot()->exists())? $this->hotspot()->first()->country : $value;
    }

    public function getCountryCodeAttribute($value)
    {
        return ($this->hotspot()->exists())? $this->hotspot()->first()->country_code : $value;
    }

    public function participants()
    {
        return $this->users()->whereIn('status', [
            ActivityInvitationStatus::INVITED,
            ActivityInvitationStatus::JOINING,
        ]);
    }

    public function getStartTimeAttribute($value)
    {
        return Carbon::parse($value)->toATOMString();
    }

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = Carbon::parse($value)->setTimezone('UTC')->toDateTimeString();
    }

    public function getEndTimeAttribute($value)
    {
        return Carbon::parse($value)->toATOMString();
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = Carbon::parse($value)->setTimezone('UTC')->toDateTimeString();
    }
}
