<?php

namespace App\Http\Controllers\V1\Auth;

use App\Contracts\Auth\AuthJwtInterface;
use App\Contracts\Storage\ImageStorageInterface;
use App\Contracts\User\UserInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\SocialLoginRequest;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Socialite;
use App\Jobs\User\UserUpdatesProfile;
use App\Jobs\User\UserSignsUp;

class AuthController extends Controller
{
    public $storageService;

    public function __construct(ImageStorageInterface $storage)
    {
        $this->storageService = $storage;
    }

    public function login(Request $request, Guard $guard, AuthJwtInterface $auth, UserInterface $userContract)
    {

        if ($guard->once($request->only('email', 'password')))
        {
            $user = $userContract->findWithRelationshipStatusAndStatistics($guard->user()->id, $guard->user()->id);

            return response()->json(
                ['data' => $user],
                201,
                ['Authorization'=> $auth->encode($guard->user()->id)]
            );
        }

        return response()->json([
            'success' => false,
            'rootMessage' => 'Invalid e-mail or password.'
        ], 403);

    }

    public function socialLogin(SocialLoginRequest $request, AuthJwtInterface $auth, UserInterface $userContract, $social)
    {
         // 1. validate token
        $socialite = Socialite::driver($social);
        $socialite->fields([
            'name', 'email', 'gender', 'verified', 'link',
            'cover', 'hometown', 'about', 'address',
            'birthday', 'languages', 'locale',
            'religion', 'sports', 'timezone'
        ]);
        try{
            $socialAccount = $socialite->userFromToken($request->get('access_token'));
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'rootMessage' => $e->getMessage()
            ], 403);
        }

        if ($socialAccount)
        {
            // 2. Check existing user
            $userExists = $userContract->isExistsByEmail($socialAccount->getEmail());
            
            if (!$userExists)
            {
                // 3. Register user
                // Cause Facebook mix firstname and lastname together.
                // Then We must split it to firstname and lastname.
                $name = $socialAccount->getName();
                $names = explode(' ', $name);
                $names = array_filter($names);

                $firstname = $names[0];

                if(count($names) > 1){
                    $lastname = end($names);
                } else {
                    $lastname = '';
                }

                $data = (array(
                    'first_name' => $firstname,
                    'last_name' => $lastname,
                    'birthday' => '1900-01-01',
                    'email' => $socialAccount->getEmail(),
                    'password' => md5($socialAccount->getEmail()),
                ));

                // Validate input.
                $v = \Validator::make($data, [
                    'first_name' => 'required|string|between:2,254',
                    'email'      => 'required|email|unique:user,email',
                ]);

                if( !$v->fails() ){
                    $data = collect($data);
                    $member = $this->dispatchFrom(UserSignsUp::class, $data);

                    // Update profile.
                    $data = array(
                        $social. '_token' => $request->get('access_token'),
                        $social. '_id' => $socialAccount->getId(),
                        'gender' => isset($socialAccount->user['gender'])?$socialAccount->user['gender']:"male",
                    );
                    $member->update($data);

                    // Update image profile and cover from facebook.
                    $this->pullFBImages($member->id, $socialAccount);
                
                    $user = $userContract->findWithRelationshipStatusAndStatistics($member->id, $member->id);

                    return response()->json(
                        ['data' => $user],
                        201,
                        ['Authorization'=> $auth->encode($member->id)]
                    );

                }else{
                    return response()->json([
                        'success' => false,
                        'rootMessage' => implode(',', $v->errors()->all()),
                    ], 403);
                }
            } 
            else 
            {
                $member = $userContract->findByEmail($socialAccount->getEmail());
                // Update profile.
                $data = array(
                    $social. '_token' => $request->get('access_token'),
                    $social. '_id' => $socialAccount->getId(),
                    'gender' => isset($socialAccount->user['gender'])?$socialAccount->user['gender']:"male",
                );
                $member->update($data);

                // Update image profile from facebook.
                //$this->pullFBImages($member->id, $socialAccount);
                
                // Get user data and send it back.
                $user = $userContract->findWithRelationshipStatusAndStatistics($member->id, $member->id);

                return response()->json(
                    ['data' => $user],
                    201,
                    ['Authorization'=> $auth->encode($member->id)]
                );
            }
        }

        return response()->json([
            'success' => false,
            'rootMessage' => 'Invalid token.'
        ], 403);
    }

    private function pullFBImages($user_id, $socialAccount)
    {
        // Must have facebook idn.
        if(!$user_id) return false;

        $profile_image_url = $socialAccount->avatar_original;
        $image_content = file_get_contents($profile_image_url);
        $this->storageService->uploadUserProfilePhoto($user_id, $image_content);

        $cover_image_url = $socialAccount->user['cover']['source'];
        $image_content = file_get_contents($cover_image_url);
        $this->storageService->uploadUserCoverPhoto($user_id, $image_content);

        return true;
    }

}
