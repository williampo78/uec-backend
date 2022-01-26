<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Members extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'members';

    protected $guarded = [];
    /**
     * 取得 JWT 辨識字串
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 回傳鍵值對陣列，內容包含被加入 JWT 的自定義 Payload
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
