<?php

namespace App\Models\Eloquent\User;

use App\Models\Eloquent\Activity\Activity;
use App\Models\Eloquent\BaseModel;
use App\Models\Eloquent\Hotspot\Hotspot;
use App\Models\Eloquent\Lists\Brand;
use App\Models\Eloquent\Lists\City;
use App\Models\Eloquent\Lists\Country;
use App\Models\Eloquent\Lists\Language;
use App\Models\Eloquent\Lists\Nationality;
use App\Models\Eloquent\Lists\Sport;
use app\Models\Enum\ContactRequestStatus;
use App\Models\Eloquent\Group\Group;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'birthday',
        'email',
        'password',
        'username',
        'shirtname',
        'gender',
        'about_me',
        'sportquote',
        'birth_country',
        'current_country',
        'current_city',
        'current_latitude',
        'current_longitude',
        'facebook_token',
        'google_token',
        'twitter_token',
        'facebook_id',
        'google_id',
        'twitter_id',
        'left',
        'top',
        'zoom'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'remember_token',
        'username',
        'facebook_token',
        'google_token',
        'twitter_token',
        'instagram_token',
        'facebook_id',
        'google_id',
        'twitter_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Attributes to be appended to a user's information
     * (Need to have a get{Name}Attribute method inside
     * this model.)
     *
     * @var array
     */
    protected $appends = [
        'age',
        'profile_photo',
        'cover_photo',
        'fullname',
    ];

    /**
     * The relations that will always be returned with a
     * user's information.
     *
     * @var array
     */
    protected $with = [
        'sports',
        'brands',
        'languages',
        'nationality',
        'hotspots'
    ];

    /**
     * Enables soft deletes
     *
     * @var array
     */
    protected $data = ['deleted_at'];

    /**
     * Set the user's age attribute based
     * on his birthday.
     *
     * @return int
     */
    public function getAgeAttribute()
    {

        return Carbon::now()
            ->startOfDay()
            ->diffInYears(
                Carbon::createFromFormat(
                    'Y-m-d',
                    $this->birthday
                )->startOfDay()
            );
    }

    public function getLeftAttribute($val)
    {
        return (double)$val;
    }

    public function getTopAttribute($val)
    {
        return (double)$val;
    }

    public function getZoomAttribute($val)
    {
        return (double)$val;
    }

    /**
     * Get user fullname
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return trim($this->first_name. ' '. $this->last_name);
    }

    /**
     * Always hash the password attribute
     *
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function setFirstNameAttribute($firstName)
    {
        $this->attributes['first_name'] = ucwords(strtolower($firstName));
    }

    public function setLastNameAttribute($lastName)
    {
        $this->attributes['last_name'] = ucwords(strtolower($lastName));
    }

    public function getProfilePhotoAttribute()
    {
        return URL::to('/files/image/profile_photo/' . $this->id);
    }

    public function getCoverPhotoAttribute()
    {
        return URL::to('/files/image/cover_photo/' . $this->id);
    }


    /**
     * Getter method to get manually set relationship status
     * @return null
     */
    public function getRelationshipStatusAttribute()
    {
        if(isset($this->attributes['relationship_status']))
            return $this->attributes['relationship_status'];
        else
            return null;
    }

    /**
     * Setter method to manually set relationship status to the model
     * @param $status
     */
    public function setRelationshipStatus($status)
    {
        $this->attributes['relationship_status'] = $status;
    }

    /**
     * Getter method to get manually set Statistics data
     * @return null
     */
    public function getStatisticsAttribute()
    {
        if(isset($this->attributes['statistics']))
            return $this->attributes['statistics'];
        else
            return null;
    }

    /**
     * Setter method to manually set Statistics to the model
     *
     * @param $totalActivitiesCreated
     * @param $totalActivitiesJoined
     */
    public function setStatistics($totalActivitiesCreated, $totalActivitiesJoined)
    {
        $this->attributes['statistics'] = array(
            'total_activity_created' => $totalActivitiesCreated,
            'total_activity_joined' => $totalActivitiesJoined,
        );
    }

    /**
     * Set many-to-many relationship with brands
     *
     * @return mixed
     */
    public function brands()
    {
        return $this->belongsToMany(
            Brand::class,
            'pivot_brand_user',
            'user_id',
            'brand_id'
        )->withTimestamps();
    }

    /**
     * Set many-to-many relationship with sports
     *
     * @return mixed
     */
    public function sports()
    {
        return $this->belongsToMany(
            Sport::class,
            'pivot_sport_user',
            'user_id',
            'sport_id'
        )->withTimestamps();
    }

    /**
     * Set one to many relationship with groups
     */
    public function groups()
    {
        return $this->belongsToMany(
            Group::class,
            'user_group',
            'user_id',
            'group_id'
        )
        ->withTimestamps()
        ->orderBy('name', 'asc');
    }

    public function myOwnGroups() {
        return $this->hasMany(
            Group::class
        );
    }

    public function myOwnSports() {
        return $this->hasMany(
            Sport::class
        );
    }

    public function myOwnHotspots() {
        return $this->hasMany(
            Hotspot::class
        );
    }

    public function myOwnActivities() {
        return $this->hasMany(
            Activity::class,
            'owner_id'
        );
    }

    /**
     * Set one-to-many relationship with hotspot
     *
     * @return mixed
     */
    public function hotspots()
    {
        return $this->belongsToMany(
            Hotspot::class,
            'pivot_hotspot_user',
            'user_id',
            'hotspot_id')
            ->withTimestamps()
            ->orderBy('name', 'asc');
    }

    /**
     * Set many-to-many relationship with languages
     *
     * @return mixed
     */
    public function languages()
    {
        return $this->belongsToMany(
            Language::class,
            'pivot_language_user',
            'user_id',
            'language_id'
        )->withTimestamps();
    }

    /**
     * Set many-to-many relationship with activities
     *
     * @return mixed
     */
    public function activities()
    {
        return $this->belongsToMany(
            Activity::class,
            'pivot_activity_user',
            'user_id',
            'activity_id'
        )->withTimestamps();
    }

    /**
     * Set many-to-one relationship with nationality entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    /**
     * Set many-to-many relationship with current user's sport contacts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function mySportContacts()
    {
        return $this->belongsToMany(
            User::class,
            'sport_contact',
            'self_id',
            'sport_contact_id'
        );
    }

    /**
     * Set many-to-many relationship with users current user is a sport contact of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sportContactOf()
    {
        return $this->belongsToMany(
            User::class,
            'sport_contact',
            'sport_contact_id',
            'self_id'
        );
    }

    /**
     * If current user is sport contacts with the user of the ID
     * provided, return them both. Else return false.
     *
     * @param $sportContactId
     *
     * @return bool|\Illuminate\Support\Collection
     */
    public function isConnectedWith($sportContactId)
    {

        $result = DB::table('sport_contact as sc1')
                    ->leftJoin('user as u1', function($join) {
                        $join->on('sc1.self_id', '=', 'u1.id');
                    })
                    ->leftJoin('user as u2', function($join) {
                        $join->on('sc1.sport_contact_id', '=', 'u2.id');
                    })
                    ->whereExists(function($query) {
                        $query->select('*')
                              ->from('sport_contact as sc2')
                              ->whereRaw('sc2.self_id = sc1.sport_contact_id');
                    })
                    ->whereIn('u1.id', [$this->id, $sportContactId])
                    ->whereIn('u2.id', [$this->id, $sportContactId])
                    ->where('sc1.deleted_at', '=', null)
                    ->get();

        return (count($result) > 0) ? collect($result) : false;

    }

    /**
     * Always generate sportContacts attribute. Will be generated
     * in loadSportContacts()
     *
     * @return mixed
     */
    public function getSportContactsAttribute()
    {
        if (!array_key_exists('sportContacts', $this->relations))
        {
            $this->loadSportContacts();
        }

        return $this->getRelation('sportContacts');
    }

    /**
     * Generate the sportContacts list/relation by merging
     * both relations in mergeSportContacts
     *
     */
    protected function loadSportContacts()
    {
        if (!array_key_exists('sportContacts', $this->relations))
        {
            $sportContacts = $this->mergeSportContacts();
            $this->setRelation('sportContacts', $sportContacts);
        }
    }

    /**
     * Merge relations of the current user's sport contacts
     * and who he is sport contact of to have a list of mutually
     * connected users.
     *
     * @return mixed
     */
    protected function mergeSportContacts()
    {
        return $this->mySportContacts->merge($this->sportContactOf);
    }

    public static function getSportContactRequest($fromId, $toId)
    {
        return DB::table('sport_contact_request')
            ->where('from', '=', $fromId)
            ->where('to', '=', $toId)
            ->first();
    }

    public static function makeSportContactRequest($fromId, $toId)
    {
        return DB::table('sport_contact_request')
            ->insert(['from' => $fromId, 'to', $toId]);
    }

    public function refreshOrNewSportContactRequest($userId)
    {
        if (!$result = static::getSportContactRequest($this->id, $userId))
        {
            return $this->makeSportContactRequest($this->id, $userId);
        }
        elseif ($result->deleted_at !== null)
        {
            DB::table('sport_contact_request')
                ->update(['deleted_at' => null]);
        }

        return false;
    }

    private function processSportContactRequest($fromId, $toId, $status)
    {
        if (!$result = DB::table('sport_contact_request')
            ->where('from', '=', $fromId)
            ->where('to', '=', $toId)
            ->update([
                'status' => $status,
                'updated_at' => Carbon::now()->toDateTimeString(),
                'deleted_at' => Carbon::now()->toDateTimeString()
            ]))
        {
            throw new HttpResponseException(new JsonResponse(['rootMessage' => 'Not found...'], 404));
        }

        return $result;
    }

    public function acceptSportContactRequest($fromId)
    {
        return $this->processSportContactRequest($fromId, $this->id, ContactRequestStatus::ACCEPTED);
    }

    public function declineSportContactRequest($fromId)
    {
        return $this->processSportContactRequest($fromId, $this->id, ContactRequestStatus::DECLINED);
    }

    public function cancelSportContactRequest($fromId)
    {
        return $this->processSportContactRequest($fromId, $this->id, ContactRequestStatus::CANCELLED);
    }

}
