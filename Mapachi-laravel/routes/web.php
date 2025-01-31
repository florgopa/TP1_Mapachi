<?php

use Illuminate\Support\Facades\Route;

// Route::get('/welcome', function () {
//     return view('welcome');
// });

Route::get('/login', function () {
    return view('TP_Mapachi.Login');
});


Route::get('/', function () {
    return view('TP_Mapachi.Index');
});

