<?php

namespace App\Http\Controllers;

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
        $this->middleware('auth');
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function create(StorePhoto $request)
    {
        $extension = $request->photo->extension();
        $photo = new Photo();

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
}
