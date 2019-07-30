<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    private $user;

    public function setUp() : void {

        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_can_add_comment() {

        factory(Photo::class)->create();
        $photo = Photo::first();

        $content = 'sample content';
        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.comment', [
                'photo' => $photo->id
            ]), compact('content'));

        $comments = $photo->comments()->get();

        $response->assertStatus(201)
            ->assertJsonFragment([
                'author' => [
                    'name' => $this->user->name
                ],
                'content' => $content
            ]);

        // DBにコメントが一件追加された
        $this->assertEquals(1, $comments->count());

        // リクエストした通りの内容か確認
        $this->assertEquals($content, $comments[0]->content);
    }
}
