<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTodoList extends Model
{
    use HasFactory;

    protected $fillable = [
    	'project_id', 'user_id_input', 'project_user_id', 'name', 'description', 'deadline_start_at', 'deadline_end_at', 'start_at', 'end_at'
    ];
}
