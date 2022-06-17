<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoListController;
use App\Http\Controllers\ProjectTodoListController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Public Routes
// AUTH
Route::prefix('auth')->group(function(){
	Route::post('register', [ AuthController::class, 'register' ]);
	Route::post('login', [ AuthController::class, 'login' ]);
});

// Protected Routes
Route::group([ 'middleware' => ['auth:sanctum'] ], function(){
	// AUTH
	Route::prefix('auth')->group(function(){
		Route::put('change-password', [ AuthController::class, 'changePassword' ]);
		Route::post('logout', [ AuthController::class, 'logout' ]);
	});

	// TODO LIST APP
	Route::prefix('todo-list')->group(function(){
		// TODO LIST APP
		Route::get('/', [ TodoListController::class, 'index' ]);
		Route::post('/', [ TodoListController::class, 'store' ]);
		Route::get('{id}', [ TodoListController::class, 'show' ]);
		Route::put('{id}', [ TodoListController::class, 'update' ]);
		Route::put('{id}/finished', [ TodoListController::class, 'finished' ]);
		Route::put('{id}/unfinished', [ TodoListController::class, 'unfinished' ]);
		Route::delete('{id}', [ TodoListController::class, 'destroy' ]);
	});

	// PROJECT TODO LIST APP
	Route::prefix('projects')->group(function(){
		Route::get('/', [ ProjectTodoListController::class, 'getProjects' ]);
		Route::post('/', [ ProjectTodoListController::class, 'storeProject' ]);
		
		Route::post('{projectId}/user/add', [ ProjectTodoListController::class, 'addUser' ]);
		Route::post('{projectId}/user/{userId}/todo', [ ProjectTodoListController::class, 'addUserTodo' ]);

		Route::prefix('{projectId}/todo/{todoId}')->group(function(){
			Route::put('start', [ ProjectTodoListController::class, 'startTodoList' ]);
			Route::put('finish', [ ProjectTodoListController::class, 'finishTodoList' ]);
		});
	});
});
