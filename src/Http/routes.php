<?php

use Strays\DcatAdminRedis\Http\Controllers\RedisController;
use Strays\DcatAdminRedis\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('dcat-admin-redis/welcome', [WelcomeController::class, 'index'])->name('dcat.admin.redis.welcome');

Route::get('dcat-admin-redis', [RedisController::class, 'index'])->name('dcat.admin.redis.index');
Route::get('dcat-admin-redis/{keys}/edit', [RedisController::class, 'edit'])->name('dcat.admin.redis.edit');
Route::get('dcat-admin-redis/{keys}', [RedisController::class, 'show'])->name('dcat.admin.redis.show');
Route::put('dcat-admin-redis/{keys}', [RedisController::class, 'update'])->name('dcat.admin.redis.update');
Route::delete('dcat-admin-redis/{keys}', [RedisController::class, 'destroy'])->name('dcat.admin.redis.destroy');




