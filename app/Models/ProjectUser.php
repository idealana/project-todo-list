<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use HasFactory;

    protected $fillable = [ 'project_id', 'user_id', 'user_id_input' ];

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
}
