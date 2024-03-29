<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::group(['middleware' => 'auth'], function(){
    Route::get('/', [PostController::class, 'index'])->name('index');
    #POST
    Route::get('/post/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/post/store', [PostController::class, 'store'])->name('post.store');
    Route::get('/post/{id}/show', [PostController::class, 'show'])->name('post.show');
    Route::get('/{id}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::patch('/{id}/update', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{id}/destroy', [PostController::class, 'destroy'])->name('post.destroy');

    #COMMENT
    Route::group(['prefix' => 'comment', 'as' => 'comment.'], function(){
        Route::post('/{post_id}/store', [CommentController::class, 'store'])->name('store');
    });
    Route::delete('/{id}/destroy', [CommentController::class, 'destroy'])->name('comment.destroy');

    #User
    Route::group(['prefix' => 'profile', 'as' =>'profile.'], function(){
        Route::get('/', [UserController::class, 'show'])->name('show');
    });
    Route::get('/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::patch('/update', [UserController::class, 'update'])->name('profile.update');
});
