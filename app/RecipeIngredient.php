<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    protected $fillable = [
        'name', 'qty'
    ];

    public $timestamps = false;

    // 用於輸出新增食譜時的API 代表Ingredient表的欄位
    public static function form() {
        return [
            'name' => '',
            'qty' => ''
        ];
    }
}
