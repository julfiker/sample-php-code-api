<?php

namespace App\Jobs\User;

use App\Contracts\User\UserInterface as UserContract;
use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Auth;

class UserUpdatesProfile extends Job implements SelfHandling
{

    /**
     * The user's first name
     *
     * @var string
     */
    public $first_name;

    /**
     * The user's last name
     *
     * @var string
     */
    public $last_name;

    /**
     * The user's birthday
     *
     * @var string
     */
    public $birthday;

    /**
     * The user's gender
     *
     * @var string
     */
    public $gender;

    /**
     * The user's shirtname
     *
     * @var string
     */
    public $shirtname;

    /**
     * The user's sportquote
     *
     * @var string
     */
    public $sportquote;

    /**
     * The user's about me information
     *
     * @var string
     */
    public $about_me;

    /**
     * The user's current city
     *
     * @var string
     */
    public $current_city;

    /**
     * The user's current country
     *
     * @var string
     */
    public $current_country;

    /**
     * The user's current country code
     *
     * @var string
     */
    public $current_country_code;

    /**
     * The user's current location latitude
     * @var double
     */
    public $current_latitude;

    /**
     * The user's current location longitude
     * @var double
     */
    public $current_longitude;

    /**
     * The user's country of birth
     *
     * @var string
     */
    public $birth_country;

    /**
     * The user's spoken languages
     *
     * @var array
     */
    public $languages = [];

    /**
     * The user's favourite brands
     *
     * @var array
     */
    public $brands = [];

    /**
     * The user's favourite sports
     *
     * @var array
     */
    public $sports = [];

    /**
     * The user's hotspot
     *
     * @var array
     */
    public $hotspots = [];

    /**
     * User's nationality
     * @var int
     */
    public $nationality_id;
    
    /**
     * User's facebook token
     * @var string
     */
    public $facebook_token;

    /**
     * User's twitter token
     * @var string
     */
    public $twitter_token;
    
    /**
     * User's google plus token
     * @var string
     */
    public $google_token;
        
    /**
     * User's facebook id
     * @var string
     */
    public $facebook_id;
        
    /**
     * User's google id
     * @var string
     */
    public $google_id;
        
    /**
     * User's twitter id
     * @var string
     */
    public $twitter_id;

    /**
     * Profile's cover left position
     * @var double
     */
    public $left;

    /**
     * Profile's cover top position
     * @var double
     */
    public $top;

    /**
     * Profile's cover zoom position
     * @var double
     */
    public $zoom;

    /**
     * Create a new job instance.
     *
     * @param null $first_name
     * @param null $last_name
     * @param null $birthday
     * @param null $gender
     * @param null $shirtname
     * @param null $sportquote
     * @param null $about_me
     * @param null $current_city
     * @param null $current_country
     * @param null $current_country_code
     * @param null $current_latitude
     * @param null $current_longitude
     * @param null $birth_country
     * @param null $nationality_id
     * @param null $languages
     * @param null $brands
     * @param null $sports
     * @param null $facebook_token
     * @param null $twitter_token
     * @param null $google_token
     * @param null $facebook_id
     * @param null $google_id
     * @param null $twitter_id
     * @param null $left
     * @param null $top
     * @param null $zoom
     */
    public function __construct(
        $first_name = null, $last_name = null, $birthday = null, $gender = null, $shirtname = null, $sportquote = null,
        $about_me = null, $current_city = null, $current_country = null, $current_country_code = null, $current_latitude = null, $current_longitude = null,
        $birth_country = null, $nationality_id = null, $languages = null, $brands = null, $sports = null, 
        $facebook_token = null, $twitter_token = null, $google_token = null,
        $facebook_id = null, $twitter_id = null, $google_id = null, $hotspots = null,
        $left = null, $top = null, $zoom = null
    )
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->birthday = $birthday;
        $this->gender = $gender;
        $this->shirtname = $shirtname;
        $this->sportquote = $sportquote;
        $this->about_me = $about_me;
        $this->current_city = $current_city;
        $this->current_country = $current_country;
        $this->current_country_code = $current_country_code;
        $this->current_latitude = $current_latitude;
        $this->current_longitude = $current_longitude;
        $this->birth_country = $birth_country;
        $this->languages = $languages;
        $this->nationality_id = $nationality_id;
        $this->brands = $brands;
        $this->sports = $sports;
        $this->facebook_token = $facebook_token;
        $this->twitter_token = $twitter_token;
        $this->google_token = $google_token;
        $this->facebook_id = $facebook_id;
        $this->google_id = $google_id;
        $this->twitter_id = $twitter_id;
        $this->hotspots = $hotspots;
        $this->left = $left;
        $this->top = $top;
        $this->zoom = $zoom;
    }

    /**
     * Update the User model with the newly received values
     * and save the model to the database.
     *
     * Values from our data lists (languages, brands and sports) have to
     * be synced separately to their pivots.
     *
     * @param UserContract $repo
     *
     * @return User
     */
    public function handle(UserContract $repo)
    {

        // Get the currently authenticated user
        $user = Auth::user();

        if ($this->first_name !== null)
            $user->first_name = $this->first_name;
        if ($this->last_name !== null)
            $user->last_name = $this->last_name;
        if ($this->birthday !== null)
            $user->birthday = $this->birthday;
        if ($this->gender !== null)
            $user->gender = $this->gender;
        if ($this->shirtname !== null)
            $user->shirtname = $this->shirtname;
        if ($this->sportquote !== null)
            $user->sportquote = $this->sportquote;
        if ($this->about_me !== null)
            $user->about_me = $this->about_me;
        if ($this->current_city !== null)
            $user->current_city = $this->current_city;
        if ($this->current_country !== null)
            $user->current_country = $this->current_country;
        if ($this->current_country_code !== null)
            $user->current_country_code = $this->current_country_code;
        if ($this->birth_country !== null)
            $user->birth_country = $this->birth_country;
        if ($this->current_latitude !== null)
            $user->current_latitude = $this->current_latitude;
        if ($this->current_longitude !== null)
            $user->current_longitude = $this->current_longitude;
        if ($this->nationality_id !== null) {
            $user->nationality_id = $this->nationality_id;
        }

        // Sync the values of received lists with their pivots
        if ($this->languages !== null) {
            $languageIds = [];
            foreach($this->languages as $language) {
                $languageIds[] = $language['id'];
            }
            $repo->sync($user, 'languages', $languageIds);
        }

        if ($this->brands !== null){
            $brandIds = [];
            foreach($this->brands as $brand) {
                $brandIds[] = $brand['id'];
            }
            $repo->sync($user, 'brands', $brandIds);
        }

        if ($this->sports !== null) {
            $sportIds = [];
            foreach($this->sports as $sport) {
                $sportIds[] = $sport['id'];
            }
            $repo->sync($user, 'sports', $sportIds);
        }

        if ($this->facebook_token !== null) {
            $user->facebook_token = $this->facebook_token;
        }

        if ($this->twitter_token !== null) {
            $user->twitter_token = $this->twitter_token;
        }

        if ($this->google_token !== null) {
            $user->google_token = $this->google_token;
        }

        if ($this->facebook_id !== null) {
            $user->facebook_id = $this->facebook_id;
        }

        if ($this->google_id !== null) {
            $user->google_id = $this->google_id;
        }

        if ($this->twitter_id !== null) {
            $user->twitter_id = $this->twitter_id;
        }

        if ($this->left !== null) {
            $user->left = $this->left;
        }

        if ($this->top !== null) {
            $user->top = $this->top;
        }

        if ($this->zoom !== null) {
            $user->zoom = $this->zoom;
        }

        if ($this->hotspots !== null) {
            $hotspotIDs = [];
            foreach($this->hotspots as $hotspot) {
                $hotspotIDs[] = $hotspot['id'];
            }
            $repo->sync($user, 'hotspots', $hotspotIDs);
        }

        // Save the updated user to the DB
        $user = $repo->save($user);

        return $user;

    }

}
