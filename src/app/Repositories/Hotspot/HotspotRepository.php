<?php  namespace App\Repositories\Hotspot; 

use App\Contracts\Hotspot\HotspotInterface;
use App\Models\Eloquent\Hotspot\Hotspot;
use Carbon\Carbon;
use Illuminate\Auth\Guard;
use Illuminate\Support\Facades\DB;

class HotspotRepository implements HotspotInterface {

    public $hotspot;
    public $auth;
    
    public function __construct(Hotspot $hotspot, Guard $auth)
    {
        $this->hotspot = $hotspot;
        $this->auth = $auth;
    }

    /**
     * Save the hotspot model to the database
     * @param Hotspot $hotspot
     * @return Hotspot
     */
    public function save(Hotspot $hotspot)
    {
        $hotspot->save();
        return $hotspot;
    }

    public function find($id)
    {
        return $this->hotspot->findOrFail($id);
    }

    public function search($conditions)
    {
        $hotspot = $this->hotspot;

        if(array_key_exists('user_id', $conditions))
        {
            $hotspot->where('user_id', $conditions['user_id']);
        }

        return $hotspot->get();
    }

    public function delete($id)
    {
        $hotspot = $this->hotspot->find($id);

        if($hotspot !== null)
        {
            return $hotspot->delete();
        }

        return ;
    }

 
}
