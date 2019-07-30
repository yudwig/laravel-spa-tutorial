<?php

namespace Tests\Feature;

use App\Comment;
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

        factory(Photo::class)->create()->each(function ($photo) {
            $photo->comments()->saveMany(
                factory(Comment::class, 3)->make()
            );
        });

        $photo = Photo::first();

        $response = $this->json('GET', route('photo.show', [
            'id' => $photo->id
        ]));

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'id'    => $photo->id,
                'url'   => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name
                ],
                'comments' => $photo->comments
                    ->sortByDesc('id')
                    ->map(function ($comment) {
                        return [
                            'author' => [
                                'name' => $comment->author->name
                            ],
                            'content' => $comment->content
                        ];
                    })
                    ->all()
            ]);
    }

}
