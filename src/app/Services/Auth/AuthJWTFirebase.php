<?php  namespace App\Services\Auth;

use App\Contracts\Auth\AuthJwtInterface;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class AuthJWTFirebase implements AuthJwtInterface
{

    public function validate(Request $request)
    {
        return $this->decode($this->extractTokenFromHeader($request));
    }

    public function extractTokenFromHeader(Request $request)
    {
        return sscanf($request->header('Authorization'), 'Bearer %s')[0];
    }

    public function encode($userID)
    {

        $time = time();

        $data = [
            'iat' => $time,
            'iss' => 'http://spoly.com',
            'aud' => 'http://api.spoly.com',
            'exp' => $time + 5184000,
            'nbf' => $time,
            'data' => [
                'userId' => $userID
            ]
        ];

        return JWT::encode($data, env('JWT_KEY'), 'HS512');

    }

    public function decode($token)
    {
        return JWT::decode($token, env('JWT_KEY'), ['HS512']);
    }

    public function currentUser($token)
    {
        return JWT::decode($token, env('JWT_KEY'), ['HS512'])->data->userId;
    }

}