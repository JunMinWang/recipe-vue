<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipe;
use App\RecipeIngredient;
use App\RecipeDirection;
use File;

class RecipeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     *  取得所有食譜
     */ 
    public function index() {
        $recipes = Recipe::orderBy('created_at', 'desc')->get(['name', 'image', 'id']);
        return response()->json([
            'recipes' => $recipes
        ]);
    }

    /**
     *  取得單一食譜
     */ 
    public function show($id) {
        $recipe = Recipe::with(['user', 'ingredients', 'directions'])->findOrFail($id);
        return response()->json([
            'recipe' => $recipe
        ]);
    }

    /**
     *  新增食譜
     */ 
    public function create() {
        $form = Recipe::form();

        return response()->json(['form' => $form]);
    }

    /**
     *  編輯食譜
     */
    public function edit($id, Request $request) {
        // user表關聯recipe表 再去分別關聯ingredients及directions表
        $form = $request->user()->recipes()->with([
            'ingredients' => function($query) {
                $query->get(['id', 'name', 'qty']);
            }, 
            'directions' => function($query) {
                $query->get(['id', 'description']);
            }])->findOrFail($id, [
                'id', 'name', 'description', 'image'
            ]);

        return response()->json([ 
            'form' => $form
        ]);
    }

    /**
     *  更新食譜
     */
    public function update($id, Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:3000',
            'image' => 'image',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'integer|exists:recipe_ingredients',
            'ingredients.*.name' => 'required|max:255',
            'ingredients.*.qty' => 'required|max:255',
            'directions' => 'required|array|min:1',
            'directions.*.id' => 'integer|exists:recipe_directions',
            'directions.*.description' => 'required|max:3000',
        ]);

        $recipe = $request->user()->recipes()->findOrFail($id);

        $ingredients = [];
        $ingredientsUpdated = [];

        foreach($request->ingredients as $ingredient) {
            if(isset($ingredient['id'])) {
                // 更新舊有食材
                RecipeIngredient::where('recipe_id', $recipe->id)
                    ->where('id', $ingredient['id'])
                    ->update($ingredient);

                $ingredientsUpdated[] = $ingredient['id'];
            } else {
                $ingredients[] = new RecipeIngredient($ingredient);
            }
        }

        $directions = [];
        $directionsUpdated = [];

        foreach($request->directions as $direction) {
            if(isset($direction['id'])) {
                // 更新舊有食材
                RecipeDirection::where('recipe_id', $recipe->id)
                    ->where('id', $direction['id'])
                    ->update($direction);

                $directionsUpdated[] = $direction['id'];
            } else {
                $directions[] = new RecipeDirection($direction);
            }
        }

        $recipe->name = $request->name;
        $recipe->description = $request->description;
        
        if($request->hasFile('image') && $request->file('image')->isValid()) {
            $filename = $this->getFileName($request->image);
            $request->image->move(base_path('public/images/'), $filename);

            // 刪除舊相片
            File::delete(base_path('public/images/' . $recipe->image));
            $recipe->image = $filename;
        }

        $recipe->save();

        // 刪除未被更新的資料
        RecipeIngredient::whereNotIn('id', $ingredientsUpdated)->where('recipe_id', $recipe->id)->delete();
        RecipeDirection::whereNotIn('id', $directionsUpdated)->where('recipe_id', $recipe->id)->delete();

        // 儲存其他新增的資料
        if(count($ingredients)) {
            $recipe->ingredients()->saveMany($ingredients);
        }

        if(count($ingredients)) {
            $recipe->directions()->saveMany($directions);
        }
        
        return response()->json([
            'saved' => true,
            'id' => $recipe->id,
            'message' => '資料更新成功'
        ]);
    }

    /**
     *  儲存食譜
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:3000',
            'image' => 'required|image',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|max:255',
            'ingredients.*.qty' => 'required|max:255',
            'directions' => 'required|array|min:1',
            'directions.*.description' => 'required|max:3000',
        ]);

        $ingredients = [];

        foreach($request->ingredients as $ingredient) {
            $ingredients[] = new RecipeIngredient($ingredient);
        }

        $directions = [];

        foreach($request->directions as $direction) {
            $directions[] = new RecipeDirection($direction);
        }

        if(!$request->hasFile('image') && !$request->file('image')->isValid()) {
            return abort(404, '請上傳圖片');
        }

        $filename = $this->getFileName($request->image);
        $request->image->move(base_path('public/images'), $filename);

        $recipe = new Recipe($request->all());

        $recipe->image = $filename;

        $request->user()->recipes()->save($recipe);

        $recipe->directions()->saveMany($directions);
        $recipe->ingredients()->saveMany($ingredients);

        return response()->json([
            'saved' => true,
            'id' => $recipe->id,
            'message' => '新增成功'
        ]);
    }

    /**
     *  刪除食譜
     */
    public function destroy($id, Request $request) {
        $recipe = $request->user()->recipes()->findOrFail($id);

        RecipeIngredient::where('recipe_id', $recipe->id)->delete();
        RecipeDirection::where('recipe_id', $recipe->id)->delete();

        File::delete(base_path('public/images/' . $recipe->image));

        $recipe->delete();

        return response()->json([
            'deleted' => true
        ]);
    }

    /**
     *  取得隨機亂碼檔名
     */
    protected function getFileName($file) {
        return str_random(32) . '.' . $file->extension();
    }
}
