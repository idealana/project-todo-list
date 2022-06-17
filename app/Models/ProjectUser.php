<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ProjectUser extends Model
{
    use HasFactory;

    protected $fillable = [ 'project_id', 'user_id', 'user_id_input' ];

    /**
     * $projectTodoList instanceof App\Models\ProjectTodoList
     */
    public ?ProjectTodoList $projectTodoList;

    /**
     * Relation belongsTo App\Models\Project
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project', 'project_id');
    }

    /**
     * Relation hasMany App\Models\ProjectTodoList
     */
    public function project_todo_lists()
    {
        return $this->hasMany('App\Models\ProjectTodoList', 'project_user_id');
    }

    public function addTodoList($request)
    {
    	$deadlineStartAt = new \DateTime(
    		$request->deadline_date_start." ".$request->deadline_time_start
    	);

    	$deadlineEndAt = new \DateTime(
    		$request->deadline_date_end." ".$request->deadline_time_end
    	);

    	return ProjectTodoList::create([
            'project_id'        => $this->project_id,
            'user_id_input'     => auth()->user()->id,
            'project_user_id'   => $this->id,
            'name'              => $request->name,
            'description'       => $request->description,
            'deadline_start_at' => $deadlineStartAt->format("Y-m-d H:i"),
            'deadline_end_at'   => $deadlineEndAt->format("Y-m-d H:i"),
        ]);
    }

    public function findProjectTodoListByColumn($column, $value)
    {
    	$this->projectTodoList = $this->project_todo_lists->where($column, $value)->first();
    	return $this;
    }

    public function isProjectTodoListEmpty(): bool
    {
    	return $this->projectTodoList instanceof ProjectTodoList === false;
    }

    public function startProjectTodoList()
    {
    	if($this->isProjectTodoListEnd()) {
    		throw ValidationException::withMessages([
                'todo_list' => "Can't start. Project Todo List already finished"
            ]);
    	}

    	if($this->isProjectTodoListStart()) {
    		throw ValidationException::withMessages([
                'todo_list' => 'Project Todo List already started'
            ]);
    	}
    	
    	$this->projectTodoList->update([ 'start_at' => now() ]);
    	return $this;
    }

    public function finishProjectTodoList()
    {
        if(! $this->isProjectTodoListStart()) {
            throw ValidationException::withMessages([
                'todo_list' => "Can't finish. Project Todo List not started"
            ]);
        }

        if($this->isProjectTodoListEnd()) {
            throw ValidationException::withMessages([
                'todo_list' => 'Project Todo List already finished'
            ]);
        }
        
        $this->projectTodoList->update([ 'end_at' => now() ]);
        return $this;
    }

    public function isProjectTodoListStart(): bool
    {
    	return is_null($this->projectTodoList->start_at) === false;
    }

    public function isProjectTodoListEnd(): bool
    {
    	return is_null($this->projectTodoList->end_at) === false;
    }
}
