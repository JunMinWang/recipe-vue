<?php

Route::post('/validate/{service}', 'AuthController@validateSocialite');
Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');

Route::resource('/recipes', 'RecipeController');