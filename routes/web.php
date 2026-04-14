<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

Route::get('/vote-button/{model}', function ($model) {
    $allowed = ['counter', 'counter2', 'button'];
    if (!in_array($model, $allowed)) abort(404);
    
    $url = "http://www.cs-servers.lt/button.php?sid=99318&model={$model}";
    $image = file_get_contents($url);
    $type = 'image/gif';
    
    return response($image, 200)->header('Content-Type', $type);
});