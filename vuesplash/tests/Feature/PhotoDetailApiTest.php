<?php

namespace Tests\Feature;

use App\Photo;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    /**
     * @test
     */
    function should_return_correct_json_format() {

        factory(Photo::class)->create();
        $photo = Photo::first();

        print_r($photo->toArray());
        print_r(Photo::where("id", $photo->id));

        $response = $this->json('GET', route('photo.show', [
            'id' => $photo->id
        ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id'    => $photo->id,
                'url'   => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name
                ]
            ]);
    }

}
