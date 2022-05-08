<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id', 'name', 'description', 'close_at' ];

    /**
     * $projectUser instanceof App\Models\ProjectUser
     */
    public ?ProjectUser $projectUser;

    /**
     * Relation hasMany App\Models\ProjectUser
     */
    public function project_users()
    {
        return $this->hasMany('App\Models\ProjectUser', 'project_id');
    }

    public function findProjectUserByColumn($column, $value)
    {
    	$this->projectUser = $this->project_users->where($column, $value)->first();
        return $this;
    }

    public function isProjectUserEmpty(): bool
    {
        return $this->projectUser instanceof ProjectUser === false;
    }

    public function storeProjectUser(User $user)
    {
    	ProjectUser::create([
            'project_id'    => $this->id,
            'user_id'       => $user->id,
            'user_id_input' => auth()->user()->id,
        ]);

        return $this;
    }

    public function addProjectTodoList($request)
    {
        $this->projectUser->addTodoList($request);
        return $this;
    }
}
