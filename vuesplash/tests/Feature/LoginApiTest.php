<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    // chapter4で書いてないけど追加。
    private $user;

    public function setUp() : void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /*
     * @test
     */
    public function shouldVerifyUser()
    {
        $response = $this->json('POST', route('login'), [
            'email' => $this->user->email,
            'password' => 'secret'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);

        $this->assertAuthenticatedAs($this->user);
    }

}
