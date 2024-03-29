<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Validation\ValidationException;

use App\Http\Resources\Resources;

class ProjectTodoListController extends Controller
{
    public function getProjects()
    {
        $user        = auth()->user();
        $getProjects = $user->project_users()->with([ 'project', 'project.project_users' ])->get();
        $projects    = Resources::collection('project_user', $getProjects);

        return response([
            'projects' => $projects,
        ]);
    }

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
    	$todoList = $project->addProjectTodoList($request);

    	return response([
    		'message'   => 'Todo List has been added',
    		'todo_list' => $todoList,
    	], Response::HTTP_CREATED);
    }

    public function startTodoList(Request $request, $projectId, $todoId)
    {
    	$user = auth()->user();

    	// check project user
    	$user->findProjectUserByColumn('project_id', $projectId);
    	if($user->isProjectUserEmpty()) {
    		return response(
    			[ 'message' => 'Project not found' ], Response::HTTP_NOT_FOUND
    		);
    	}

    	$projectUser = $user->projectUser;

    	// check todo list
    	$projectUser->findProjectTodoListByColumn('id', $todoId);
    	if($projectUser->isProjectTodoListEmpty()) {
    		return response(
    			[ 'message' => 'Project Todo List not found' ], Response::HTTP_NOT_FOUND
    		);
    	}

    	// start todo list
    	$projectUser->startProjectTodoList();

    	return response([
    		'message' => 'Todo List has been started',
    	]);
    }

    public function finishTodoList(Request $request, $projectId, $todoId)
    {
        $user = auth()->user();

        // check project user
        $user->findProjectUserByColumn('project_id', $projectId);
        if($user->isProjectUserEmpty()) {
            return response(
                [ 'message' => 'Project not found' ], Response::HTTP_NOT_FOUND
            );
        }

        $projectUser = $user->projectUser;

        // check todo list
        $projectUser->findProjectTodoListByColumn('id', $todoId);
        if($projectUser->isProjectTodoListEmpty()) {
            return response(
                [ 'message' => 'Project Todo List not found' ], Response::HTTP_NOT_FOUND
            );
        }

        // finish todo list
        $projectUser->finishProjectTodoList();

        return response([
            'message' => 'Todo List has been finished',
        ]);
    }

    public function detail($projectId)
    {
        $user = auth()->user();

        // check project user
        $user->findProjectUserByColumn('project_id', $projectId);
        if($user->isProjectUserEmpty()) {
            return response(
                [ 'message' => 'Project not found' ],
                Response::HTTP_NOT_FOUND
            );
        }

        $projectUser = $user->projectUser;
        $project     = $projectUser->project;
        $users       = $project->project_users()->with([ 'user', 'user_input', 'project_todo_lists' ])->get();

        $projectResource = Resources::new('project', $project);
        $userCollections = Resources::collection('project_user_lists', $users);

        return response([
            'project' => $projectResource,
            'users'   => $userCollections,
        ]);
    }
}
