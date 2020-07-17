<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class PasswordControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testChangePassword()
    {

        $this->withoutMiddleware();

        $user = factory(App\Models\Eloquent\User\User::class)->create([
            'password' => 'superSecret',
        ]);
        $this->post('/auth/login', ['email'=>$user->email, 'password' => 'superSecret'])
            ->seeStatusCode(Response::HTTP_CREATED);

        // password does not match, should not change
        $this->actingAs($user);
        $this->put('/passwords/change', [
            'current_password' => 'bad_password',
            'new_password' => '12345678',
            'new_password_confirmation' => '87654321',
        ])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        // bad password, should not change
        $this->actingAs($user);
        $this->put('/passwords/change', [
            'current_password' => 'bad_password',
            'new_password' => '12345678',
            'new_password_confirmation' => '12345678',
        ])->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);

        // correct password, should be updated
        $this->actingAs($user);
        $this->put('/passwords/change', [
            'current_password' => 'superSecret',
            'new_password' => '12345678',
            'new_password_confirmation' => '12345678',
        ])->seeStatusCode(Response::HTTP_NO_CONTENT);

        $this->post('/auth/login', ['email' => $user->email, 'password' => '12345678'])
            ->seeStatusCode(Response::HTTP_CREATED);
    }

    public function testResetPassword()
    {
        $user = factory(App\Models\Eloquent\User\User::class)->create([
            'email' => 'sarojroy@gmail.com',
            'password' => 'superSecret',
        ]);

        $this->expectsEvents([
            App\Events\PasswordReset::class,
        ]);

        $this->post('/passwords/reset', ['email' => $user->email])
            ->seeStatusCode(Response::HTTP_CREATED);
    }
}
