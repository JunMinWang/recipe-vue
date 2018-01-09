<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeDirection extends Model
{
    protected $fillable = ['description'];

    public $timestamps = false;

    // 用於輸出新增食譜時的API 代表description表的欄位
    public static function form() {
        return [
            'description' => ''
        ];
    }
}
