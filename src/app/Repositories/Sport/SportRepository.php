<?php  namespace App\Repositories\Sport; 

use App\Contracts\Sport\SportInterface;
use App\Models\Eloquent\Lists\Sport;
use Illuminate\Support\Facades\DB;

class SportRepository implements SportInterface {

    private $sport;
    
    public function __construct(Sport $sport)
    {
        $this->sport = $sport;
    }

    /**
     * Save the sport model to the database
     * @param Sport $sport
     * @return Sport
     */
    public function save(Sport $sport)
    {
        $sport->save();
        return $sport;
    }

    public function find($id)
    {
        return $this->sport->findOrFail($id);
    }

    public function delete($id)
    {
        $sport = $this->sport->find($id);

        if($sport !== null)
        {
            return $sport->delete();
        }

        return ;
    }

    public function updatePivotSportUser($source, $destination)
    {
        DB::table('pivot_sport_user')
            ->where('sport_id', $source)
            ->update(array('sport_id' => $destination));
    }

}
