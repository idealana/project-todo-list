<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todoLists = auth()->user()->todo_lists;

        return response([
            'todoLists' => $todoLists,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todoList = TodoList::create($request->only('name', 'description', 'user_id'));
        $response = [
            'todoList' => $todoList,
            'message'  => 'Todo List has been created',
        ];

        return response($response, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todoList = TodoList::find($id);

        if(! $todoList) {
            return response([
                'message' => sprintf('Todo List with id %s not found', $id),
            ], Response::HTTP_NOT_FOUND);
        }

        return response([
            'todoList' => $todoList,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function edit(TodoList $todoList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todoList = auth()->user()->find_todo_list($id);

        if(! $todoList) {
            return response([
                'message' => sprintf('Todo List with id %s not found', $id),
            ], Response::HTTP_NOT_FOUND);
        }

        $todoList->update($request->only('name', 'description'));

        return response([
            'todoList' => $todoList,
            'message'  => 'Todo List has been updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $todoList = auth()->user()->find_todo_list($id);

        if(! $todoList) {
            return response([
                'message' => sprintf('Todo List with id %s not found', $id),
            ], Response::HTTP_NOT_FOUND);
        }

        $todoList->delete();

        return response([
            'message'  => sprintf('Todo List with id %s has been deleted', $id),
        ]);
    }

    public function finished($id)
    {
        $todoList = auth()->user()->find_todo_list($id);

        if(! $todoList) {
            return response([
                'message' => sprintf('Todo List with id %s not found', $id),
            ], Response::HTTP_NOT_FOUND);
        }

        if($todoList->isFinished()) {
            return response([
                'message' => 'Todo List already finished',
            ]);
        }

        $todoList->setFinished()->save();

        return response([
            'message'  => 'Todo List has been finished',
            'todoList' => $todoList,
        ]);
    }

    public function unfinished($id)
    {
        $todoList = auth()->user()->find_todo_list($id);

        if(! $todoList) {
            return response([
                'message' => sprintf('Todo List with id %s not found', $id),
            ], Response::HTTP_NOT_FOUND);
        }

        if(! $todoList->isFinished()) {
            return response([
                'message' => 'Todo List already unfinished',
            ]);
        }

        $todoList->setUnfinished()->save();

        return response([
            'message'  => 'Todo List has been unfinished',
            'todoList' => $todoList,
        ]);
    }
}
