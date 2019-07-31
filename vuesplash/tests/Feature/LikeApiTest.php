<?php

namespace Tests\Feature;

use App\User;
use App\Photo;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeApiTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    private $user;
    private $photo;

    public function setUp() : void {

        parent::setUp();
        $this->user = factory(User::class)->create();
        factory(Photo::class)->create();
        $this->photo = Photo::first();
    }

    /**
     * @test
     */
    public function should_add_like() {

        $response = $this->actingAs($this->user)
            ->json('PUT', route('photo.like', [
                'photo' => $this->photo->id
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'photo_id' => $this->photo->id
            ]);

        $this->assertEquals(1, $this->photo->likes()->count());
    }

    /**
     * @test
     */
    public function should_only_allow_single_like() {

        $param = ['id' => $this->photo->id];
        $this->actingAs($this->user)
            ->json('PUT', route('photo.like', $param));

        $this->actingAs($this->user)
            ->json('PUT', route('photo.like', $param));


        $this->assertEquals(1, $this->photo->likes()->count());
    }

    /**
     * @test
     */
    public function should_can_cancel_like() {

        $this->photo->likes()->attach($this->user->id);

        $response = $this->actingAs($this->user)
            ->json('DELETE', route('photo.like', [
                'photo' => $this->photo->id
            ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'photo_id' => $this->photo->id
            ]);

        $this->assertEquals(0, $this->photo->likes()->count());
    }

}
