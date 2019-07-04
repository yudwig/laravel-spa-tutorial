<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;
    private $user;

    public function setUp() : void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function shouldUploadFile()
    {
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg')
            ]);

        $response->assertStatus(201);

        $photo = Photo::first();

        // IDが12桁になっていること
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);
        Storage::cloud()->assertExists($photo->filename);
    }

    /**
     * @test
     * 通信エラーが発生した際に、データがDBに登録されないことを確認
     */
    public function shouldNotSaveIfDatabaseError()
    {
        // DBエラーを発生させる
        Schema::drop('photos');
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg')
            ]);

        $response->assertStatus(500);
        $this->assertEquals(0, count(Storage::cloud()->files()));
    }

    /**
     * @test
     * ファイル保存時にエラーが発生した際に、データがDBに登録されないことを確認
     */
    public function shouldNotInsertIfDatabaseError()
    {
        // ストレージをモックする
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg')
            ]);

        $response->assertStatus(500);

        // DBに何も挿入されていない
        $this->assertEmpty(Photo::all());
    }
}
