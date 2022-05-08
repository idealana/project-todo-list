<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ProjectTodoListController extends Controller
{
    public function storeProject(Request $request)
    {
    	$request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

    	$user = auth()->user();
		$user->createProject([
			'name'        => $request->name,
			'description' => $request->description,
		]);

    	$response = [
    		'message' => 'Project has been created',
    		'project' => $user->project,
    	];

    	return response($response, Response::HTTP_CREATED);
    }

    public function addUser(Request $request, $projectId)
    {
    	$request->validate([
    		'email' => 'required|email'
    	]);

    	$user = auth()->user();
    	$user->findProjectById($projectId);
    	
    	if($user->isProjectEmpty()) {
    		return response(
    			[ 'message' => 'Project not found' ], Response::HTTP_NOT_FOUND
    		);
    	}

    	$findUser = User::findByEmail($request->email);
    	if(! $findUser) {
    		throw ValidationException::withMessages([
                'email' => 'User email not found',
            ]);
    	}

    	$user->checkAndStoreProjectUser($findUser);

    	$response = [
    		'message' => 'User has been added',
    	];

    	return response($response, Response::HTTP_CREATED);
    }

    public function addUserTodo(Request $request, $projectId, $userId)
    {
    	$request->validate([
    		'name'                => 'required|string|max:255',
    		'description'         => 'nullable|string',
    		'deadline_date_start' => 'required|date_format:Y-m-d',
    		'deadline_time_start' => 'required|date_format:H:i',
    		'deadline_date_end'   => 'required|date_format:Y-m-d',
    		'deadline_time_end'   => 'required|date_format:H:i',
    	]);

    	$user = auth()->user();

    	// check project
    	$user->findProjectById($projectId);
    	if($user->isProjectEmpty()) {
    		return response(
    			[ 'message' => 'Project not found' ], Response::HTTP_NOT_FOUND
    		);
    	}

    	$project = $user->project;

    	// check project user
    	$project->findProjectUserByColumn('user_id', $userId);
    	if($project->isProjectUserEmpty()) {
    		return response(
    			[ 'message' => 'User not found in this project' ], Response::HTTP_NOT_FOUND
    		);
    	}

    	// store todo list
    	$project->addProjectTodoList($request);

    	return response([
    		'message' => 'Todo List has been added',
    	], Response::HTTP_CREATED);
    }
}
