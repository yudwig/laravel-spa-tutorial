<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterApiTest extends TestCase
{
    // chapter.4のテストが初回しか通らなかったため追加。テスト実行の度にレコード削除。
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * @test
     *
     * chapter4でshould_新しいユーザーを作成して返却する()とあったが、
     * 日本語も使えるらしい。使わないけど。
     */
    public function shouldCreateNewUser()
    {
        $data = [
            "name" => "vuesplash user",
            "email" => "dummy@email.com",
            "password" => "test1234",
            "password_confirmation" => "test1234"
        ];

        $response = $this->json("POST", route("register"), $data);
        $user = User::first();
        $this->assertEquals($data["name"], $user->name);

        $response
            ->assertStatus(201)
            ->assertJson(["name" => $user->name]);
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
}
