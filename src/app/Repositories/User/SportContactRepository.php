<?php  namespace App\Repositories\User; 

use App\Contracts\User\SportContactInterface;
use App\Models\Eloquent\Notification\SportContact;
use App\Models\Eloquent\Notification\SportContactRequest;
use App\Models\Eloquent\User\User;

class SportContactRepository implements SportContactInterface {

    private $sportContactEloquent;
    private $sportContactRequestEloquent;
    private $userEloquent;

    public function __construct(User $user, SportContact $sportContact, SportContactRequest $sportContactRequest)
    {
        $this->userEloquent = $user;
        $this->sportContactEloquent = $sportContact;
        $this->sportContactRequestEloquent = $sportContactRequest;
    }

    public function findFriends($userId) {
        $myContacts = $this->userEloquent
            ->select('user.*')
            ->join('sport_contact', 'user.id', '=', 'sport_contact.sport_contact_id')
            ->where('sport_contact.self_id', '=', $userId)
            ->get()
            ->toArray();

        $contactOf = $this->userEloquent
            ->select('user.*')
            ->join('sport_contact', 'user.id', '=', 'sport_contact.self_id')
            ->where('sport_contact.sport_contact_id', '=', $userId)
            ->get()
            ->toArray();

        return array_merge($myContacts, $contactOf);
    }

    public function delete($selfId, $sportContactId)
    {
        $this->sportContactEloquent
            ->whereIn('self_id', [$selfId, $sportContactId])
            ->whereIn('sport_contact_id', [$selfId, $sportContactId])
            ->delete();
        $this->sportContactRequestEloquent
            ->whereIn('from', [$selfId, $sportContactId])
            ->where('to', [$selfId, $sportContactId])
            ->delete();
    }
}