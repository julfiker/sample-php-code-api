<?php

namespace App\Jobs\Hotspot;

use App\Events\HotspotCreated;
use App\Jobs\Job;
use App\Models\Eloquent\Hotspot\Hotspot;
use App\Repositories\Hotspot\HotspotRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Event;

class CreateHotspotJob extends Job implements SelfHandling
{
    public $name;
    public $category_id;
    public $lat;
    public $long;
    public $street;
    public $street_number;
    public $address;
    public $city;
    public $country;
    public $country_code;
    public $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name, $category_id, $lat, $long, $street = '', $street_number = '', $address = '', $city, $country, $country_code = '', $user_id)
    {
        $this->name = $name;
        $this->category_id = $category_id;
        $this->lat = $lat;
        $this->long = $long;
        $this->street = $street;
        $this->street_number = $street_number;
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->country_code = $country_code;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @param Hotspot $hotspot
     * @param HotspotRepository $repository
     * @return Hotspot
     */
    public function handle(Hotspot $hotspot, HotspotRepository $repository)
    {
        $hotspot = $repository->save($hotspot->fill(get_object_vars($this)));
        Event::fire(new HotspotCreated($hotspot));
        return $hotspot;
    }
}
