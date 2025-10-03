<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->middleware('guest')->name('login');
Route::view('/users', 'admin.user')->middleware('auth')->name('users');
Route::view('/', 'index')->middleware('auth')->name('index');
Route::view('/brokers', 'page.broker')->middleware('auth')->name('brokers');
Route::view('/chemicals/{id?}', 'page.chemical')->middleware('auth')->name('chemicals');
Route::view('/inventory/{id?}', 'page.inventory')->middleware('auth')->name('inventory');
Route::view('/setting', 'page.setting')->middleware('auth')->name('setting');

Route::view('/requisitions/{id?}', 'page.requisitions.index')->middleware('auth')->name('requisitions');
Route::view('/requisitions/create/{id?}', 'page.requisitions.create')->middleware('auth')->name('requisitions.create');
Route::view('/requisitions/view/{id?}', 'page.requisitions.view')->middleware('auth')->name('requisitions.view');
Route::get('/planschedules', [PlanScheduleController::class, 'index']);