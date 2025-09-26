<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->middleware('guest')->name('login');
Route::view('/users', 'admin.user')->middleware('auth')->name('users');
Route::view('/', 'index')->middleware('auth')->name('index');
