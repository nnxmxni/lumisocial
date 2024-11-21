<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\AccountController;
use App\Http\Middleware\EnsureUserIsASprintMember;

Broadcast::routes(['prefix' => 'v1', 'middleware' => ['auth:sanctum']]);

Route::prefix('v1')->group(function (){

    Route::get('user', function (Request $request) {
        $data['user'] = new \App\Http\Resources\UserResource($request->user());
        return response()->json([
            'status' => true,
            'message' => 'User information retrieved successfully',
            'data' => $data,
        ]);
    })->middleware('auth:sanctum');

   Route::controller(AuthController::class)->group(function (){
        Route::post('register', 'register');
        Route::post('login', 'login');
   });

   Route::controller(AccountController::class)->group(function (){
       Route::post('prepassword', 'prepassword');
       Route::post('postpassword', 'postpassword');
       Route::post('logout', 'logout')->middleware('auth:sanctum');
   });

   Route::middleware('auth:sanctum')->group(function (){

       Route::controller(SearchController::class)->group(function (){
           Route::any('search', 'searchByUsername');
       });

       Route::controller(SprintController::class)->group(function (){
           Route::prefix('sprint')->group(function (){
               Route::post('create', 'store');
               Route::get('index', 'index');
               Route::prefix('{sprint:slug}')->group(function (){
                   Route::middleware(EnsureUserIsASprintMember::class)->group(function (){
                       Route::get('/', 'show');
                       Route::post('invite/{user:username}', 'sendInviteToUser');
                       Route::post('completed', 'updateSprintCompletedStatus');
                       Route::post('remove/{user:username}', 'removeMemberFromSprint');
                       Route::post('exit', 'exit');
                       Route::patch('update', 'update');
                       Route::delete('delete', 'delete');
                   });
                   Route::get('{user:username}/accept/invite', 'acceptInvitation')->name('invite.user');
               });
           });
       });

       Route::controller(TaskController::class)->group(function (){
           Route::middleware(EnsureUserIsASprintMember::class)->group(function (){
               Route::prefix('task')->group(function (){
                   Route::prefix('{sprint:slug}')->group(function (){
                       Route::post('create', 'store');
                       Route::get('index', 'index');
                       Route::prefix('{task:slug}')->group(function (){
                           Route::get('/', 'show');
                           Route::patch('update', 'update');
                           Route::delete('delete', 'delete');
                       });
                   });
               });
           });
       });
   });
});
