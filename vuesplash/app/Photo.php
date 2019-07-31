<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
   protected $keyType = 'string';
   protected $perPage =  6;
   protected $appends = [
       'url', 'likes_count', 'liked_by_user'
   ];

   protected $hidden = [
       'user_id', 'filename',
       self::CREATED_AT, self::UPDATED_AT
   ];

   protected $visible = [
       'id', 'owner', 'url', 'comments',
       'likes_count', 'liked_by_user'
   ];

   const ID_LENGTH = 12;

   public function __construct(array $attributes = [])
   {
       parent::__construct($attributes);

       // 必ず呼び出されるようにコンストラクタで呼び出す とのこと。
       if (! array_get($this->attributes, 'id')) {
           $this->setId();
       }
   }

   /**
    * ランダムなIDをid属性に代入
    */
   private function setId()
   {
       $this->attributes['id'] = $this->getRandomId();
   }

    /**
     * ランダムなIDを生成
     * @return string
     * @throws \Exception
     */
   private function getRandomId()
   {
       $characters = array_merge(
           range(0, 9),
           range('a', 'z'),
           range('A', 'Z'),
           ['-', '_']
       );
       $length = count($characters);
       $id = '';
       for ($i = 0; $i < self::ID_LENGTH; $i++) {
           $id .= $characters[random_int(0, $length - 1)];
       }
       return $id;
   }

   public function owner() {
       return $this->belongsTo('App\User', 'user_id', 'id', 'users');
   }

   public function likes() {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
   }

   public function comments() {
       return $this->hasMany('App\Comment')->orderBy('id', 'desc');
   }

   public function getLikesCountAttribute() {
       return $this->likes->count();
   }

   public function getLikedByUserAttribute() {

       if (Auth::guest()) {
           return false;
       }

       // likeを付けたユーザーの中に自分が含まれるかどうか
       return $this->likes->contains(function($user) {
           return $user->id === Auth::user()->id;
       });
   }

   public function getUrlAttribute() {
       return Storage::cloud()->url($this->attributes['filename']);
   }
}
