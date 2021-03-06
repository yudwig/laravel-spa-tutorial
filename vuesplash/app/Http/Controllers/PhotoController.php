<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\StoreComment;
use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function create(StorePhoto $request)
    {
//        file_put_contents("./storage/logs/mylog", print_r($request, true), FILE_APPEND);

        $extension = $request->photo->extension();
        $photo = new Photo();
//        file_put_contents("./storage/logs/mylog", print_r($photo, true), FILE_APPEND);

        $photo->filename =  $photo->id . '.' . $extension;
        Storage::cloud()->putFileAs('', $request->photo, $photo->filename, 'public');

        // DBエラー時にファイル削除したいのでトランザクションを利用
        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Storage::cloud()->delete($photo->filename);
            throw $exception;
        }

        // 新規作成なのでレスポンスコードは201(CREATED)を返却
        return response($photo, 201);
    }

    public function index() {
        $photos = Photo::with(['owner', 'likes'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();
        return $photos;
    }

    public function show(string $id) {

        $photo = Photo::where('id', $id)
            ->with(['owner', 'comments.author', 'likes'])->first();
        return $photo ?? abort(404);
    }

    public function download(Photo $photo) {

        if (!Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment: filename="' . $photo->filename . '"',
        ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

    public function addComment(Photo $photo, StoreComment $request) {

        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }

    public function like(string $id) {

        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }

        // 二回実行されてしまった場合の対策
        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ['photo_id' => $id];
    }

    public function unlike(string $id) {

        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        return ['photo_id' => $id];
    }
}
