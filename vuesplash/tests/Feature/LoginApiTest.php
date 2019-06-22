<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    // chapter4で書いてないけど追加。
    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shouldVerifyUser()
    {

        $response = $this->json('POST', route('login'), [
            'email' => $this->user->email,
//            'password' => 'secret'

            // ↓ laravel 5.7からこうなったらしい。デバッガで追ってみたが断念した。
            // (そもそも'password' => $this->user->password ではない理由からよくわからないが、一時休戦)
            'password' => 'password'

        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);

        $this->assertAuthenticatedAs($this->user);
    }
}
