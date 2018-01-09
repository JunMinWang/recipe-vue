<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = ['name', 'description', 'image'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function ingredients() {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function directions() {
        return $this->hasMany(RecipeDirection::class);
    }

    // 用於輸出新增食譜時的API 代表Recipe表的欄位
    public static function form() {
        return [
            'name' => '',
            'decription' => '',
            'image' => '',
            'ingredients' => [
                RecipeIngredient::form()
            ],
            'directions' => [
                RecipeDirection::form(),
                RecipeDirection::form() 
            ]
        ];
    }
}
