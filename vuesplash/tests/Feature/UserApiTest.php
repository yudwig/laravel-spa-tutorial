<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shouldGetLoginUser()
    {
        // setUpで作成したユーザーとしてログインした状態でアクセス
        $response = $this->actingAs($this->user)->json('GET', route('user'));
        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name
            ]);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyNameIfNotLogin()
    {
        $response = $this->json('GET', route('user'));
        $response->assertStatus(200);
        $this->assertEquals("", $response->content());
    }

}
