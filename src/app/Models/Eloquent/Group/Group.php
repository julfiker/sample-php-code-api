<?php

namespace App\Models\Eloquent\Group;

use App\Models\Eloquent\BaseModel;
use App\Models\Eloquent\User\User;
use App\Models\Eloquent\Group\UserGroup;

class Group extends BaseModel
{
    protected $table = 'group';

    protected $fillable = ['name', 'user_id', 'is_private'];

    protected $hidden = ['created_at', 'updated_at', 'role'];

    protected $appends = [
        'owner'
    ];

    public function owner()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    public function members()
    {
        return $this->belongsToMany(
             User::class,
            'user_group',
            'group_id',
            'user_id'
        )->withPivot('status');
    }

    public function getMembersAttribute($value) {
        $items = array();
        $members = $this->members()->get();
        foreach ($members as $user) {
            $items[] = array("id" => $user->id, "name" => $user->fullname);
        }

        return $items;
    }

    public function getOwnerAttribute($value)
    {
        return ($this->owner()->exists())? $this->owner()->first()->fullname : $value;
    }

}
