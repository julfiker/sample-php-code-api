<?php  namespace App\Repositories\User; 

use App\Contracts\Activity\ActivityInterface;
use App\Contracts\User\UserInterface as UserContract;
use App\Models\Enum\ContactRequestStatus;
use App\Models\Eloquent\Notification\SportContact;
use App\Models\Eloquent\Notification\SportContactRequest;
use App\Models\Eloquent\User\User as UserEloquent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Exception\NotFoundException;

class UserRepository implements UserContract {

    private $userEloquent;
    private $sportContact;
    private $sportContactRequest;
    private $activityInterface;

    public function __construct(
        UserEloquent $user,
        SportContact $sportContact,
        SportContactRequest $sportContactRequest,
        ActivityInterface $activityInterface
    )
    {
        $this->userEloquent = $user;
        $this->sportContact = $sportContact;
        $this->sportContactRequest = $sportContactRequest;
        $this->activityInterface = $activityInterface;
    }

    /**
     * Use Eloquent's findOrFail to find user by ID
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->userEloquent->findOrFail($id);
    }

    /**
     * Get a user with two additional information:
     *  1. relationship status with the current user
     *  2. statistics
     *
     * @param $targetUserId
     * @param $currentUserId
     * @return mixed
     */
    public function findWithRelationshipStatusAndStatistics($targetUserId, $currentUserId)
    {
        $user = $this->find($targetUserId);
        // set the contact connection status
        $user->setRelationshipStatus($this->_getRelationshipStatus($targetUserId, $currentUserId));

        // set statistics data
        $user->setStatistics(
            $this->activityInterface->countOwnActivity($targetUserId),
            $this->activityInterface->countJoinedActivity($targetUserId)
        );

        return $user;
    }

    /**
     * Helper function to get relationship status
     *
     * @param $targetUserId
     * @param $currentUserId
     * @return string
     */
    private function _getRelationshipStatus($targetUserId, $currentUserId) {

        if ($this->isConnected($currentUserId, $targetUserId))
            return "connected";

        if ($this->isRequestPending($currentUserId, $targetUserId))
            return "pending";

        if ($this->isRequestReceived($targetUserId, $currentUserId))
            return "request_received";

        return "not_connected";
    }

    /**
     * Simple search to find users (skipping friends) by matching first name and last name
     *
     *
     * TODO: Make the search efficient by using Solr or Elastic search
     *
     * @param $searchTerm
     * @param $userIdToSkip
     * @return mixed
     */
    public function search($searchTerm, $userIdToSkip, $request)
    {
        $searchTerm = "%{$searchTerm}%";
        $pageSize = Config::get('constants.page_size');
        $statement = $this->userEloquent
             ->where('id', '<>', $userIdToSkip)
            // skipping friends
            ->whereNotIn('user.id',function($query) use ($userIdToSkip){
                // my sport contacts
                $query
                    ->select('sport_contact_id')
                    ->from('sport_contact')
                    ->where('self_id', $userIdToSkip)
                ;
            })
            // skipping friends
            ->whereNotIn('user.id',function($query) use ($userIdToSkip){
                // sport contact of
                $query
                    ->select('self_id')
                    ->from('sport_contact')
                    ->where('sport_contact_id', $userIdToSkip)
                ;
            })
            ->where(DB::raw('CONCAT_WS(" ", user.first_name, user.last_name)'), "LIKE", $searchTerm);

        //Search by country based on user
        if ($country = trim($request->get('country'))) {
            $statement->where('user.current_country', "LIKE", $country."%");
        }

        //Search by city for user
        if ($city = trim($request->get('city'))) {
            $statement->where('user.current_city', "LIKE", $city."%");
        }

        //Searching by nationality
        if ($nationality = trim($request->get('nationality'))) {
            $statement->join(
                'nationality',
                'nationality.id','=','user.nationality_id'
            )
                ->where('nationality.name', "LIKE", $nationality."%");
        }

        //Searching by languages
        if ($lang = trim($request->get('language'))) {
            $statement->whereHas('languages', function ($query) use ($lang) {
                $query->where('name', 'like', $lang."%");
            });
        }

        //Searching by sports
        if ($sport = trim($request->get('sport'))) {
            $statement->whereHas('sports', function ($query) use ($sport) {
                $query->where('name', 'like', $sport."%");
            });
        }

        return $statement->paginate($pageSize);
    }

    /**
     * Save the user model to the database
     * @param UserEloquent $user
     * @return UserEloquent
     */
    public function save(UserEloquent $user)
    {
        $user->save();
        return $user;
    }

    /**
     * Sync the provided relation with the DB
     *
     * @param UserEloquent $user
     * @param $relation
     * @param array $values
     * @param bool|true $remove
     *
     * @return Eloquent
     */
    public function sync(UserEloquent $user, $relation, $values = [], $remove = true)
    {

        // TODO Change the name of the method
        // TODO Separate all syncable relations to their own method
        $user->$relation()->sync($values, $remove);
        return $user;
    }

    /**
     * Soft delete account
     *
     * @param $id
     *
     * @return mixed
     */
    public function disableAccount($id)
    {
        $user = $this->userEloquent->findOrFail($id);
        $user->email = 'deleted-'. $user->id . '-' . $user->email;
        $user->save();
        $user->delete();
        return $user;
    }

    public function isConnected($fromUserId, $toUserId)
    {
        $count = $this->sportContact->whereRaw(
            'deleted_at IS NULL AND ((self_id = ? and sport_contact_id = ?) OR (self_id = ? and sport_contact_id = ?))',
            [$fromUserId, $toUserId, $toUserId, $fromUserId]
        )->count();
        if ($count > 0)
            return true;
        return false;
    }

    public function isRequestPending($fromUserId, $toUserId)
    {
        $count = $this->sportContactRequest
            ->where('from', $fromUserId)
            ->where('to', $toUserId)
            ->where('status', ContactRequestStatus::PENDING)
            ->count();

        if ($count > 0)
            return true;
        return false;
    }

    public function isRequestReceived($fromUserId, $toUserId)
    {
        $count = $this->sportContactRequest
            ->where('from', $fromUserId)
            ->where('to', $toUserId)
            ->where('status', ContactRequestStatus::PENDING)
            ->count();

        if ($count > 0)
            return true;
        return false;
    }



    public function isRequestDeclined($fromUserId, $toUserId)
    {
        $count = $this->sportContactRequest
            ->where('from', $fromUserId)
            ->where('to', $toUserId)
            ->where('status', ContactRequestStatus::DECLINED)
            ->count();

        if ($count > 0)
            return true;
        return false;
    }

    public function connect($contactUserId, $selfId)
    {
        $contact = new SportContact();
        $contact->self_id = $selfId;
        $contact->sport_contact_id = $contactUserId;
        return $contact->save();
    }

    public function changePassword($userId, $newPassword)
    {
        $user = $this->userEloquent->findOrFail($userId);
        $user->password = $newPassword;
        $user->save();
    }

    public function findByEmail($email) 
    {
        $user =  $this->userEloquent
            ->where('email', '=', $email)
            ->first();
        if ($user == null)
            throw new NotFoundException("User with email: {$email} is not found");

        return $user;
    }

    public function isExistsByEmail($email)
    {
        return $this->userEloquent
            ->where('email', '=', $email)
            ->exists();
    }

    public function resetPassword($user)
    {
        $newPassword = $this->generateRandomString(12);
        $this->changePassword($user->id, $newPassword);
        return $newPassword;
    }

    /**
     * Generates random alphanumeric string
     * @param $randomStringLength
     * @return string
     */
    private function generateRandomString($randomStringLength) {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        for ($i = 0; $i < $randomStringLength; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }

}