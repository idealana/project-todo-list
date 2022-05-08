<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relation hasMany to TodoList
     */
    public function todo_lists()
    {
        return $this->hasMany('App\Models\TodoList', 'user_id');
    }

    public function find_todo_list($id)
    {
        return $this->todo_lists->find($id);
    }

    public function getToken()
    {
        return $this->createToken('L4R4V3L-4P1')->plainTextToken;
    }

    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * PROJECT TODO LIST APP
     */

    /**
     * $project instanceof App\Models\Project
     */
    public ?Project $project;

    /**
     * $projectUser instanceof App\Models\ProjectUser
     */
    public ?ProjectUser $projectUser;

    /**
     * Relation hasMany App\Models\Project
     */
    public function projects()
    {
        return $this->hasMany('App\Models\Project', 'user_id');
    }

    /**
     * Relation hasMany App\Models\ProjectUser
     */
    public function project_users()
    {
        return $this->hasMany('App\Models\ProjectUser', 'user_id');
    }

    public function createProject(array $request)
    {
        $this->project = Project::create([
            'user_id'     => $this->id,
            'name'        => $request['name'],
            'description' => $request['description'],
        ]);

        // Insert Project Owner
        $this->addUserToProject($this);

        return $this;
    }

    public function addUserToProject(User $user)
    {
        $this->project->storeProjectUser($user);
        return $this;
    }

    public function findProjectById($id)
    {
        $this->project = $this->projects->find($id);
        return $this;
    }

    public function isProjectEmpty(): bool
    {
        return $this->project instanceof Project === false;
    }

    public function checkAndStoreProjectUser(User $user)
    {
        $this->project->findProjectUserByColumn('user_id', $user->id);

        if(! $this->project->isProjectUserEmpty()) {
            // if user exist
            throw ValidationException::withMessages([
                'email' => 'User already exist in this project'
            ]);
        }

        $this->addUserToProject($user);
        return $this;
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
}
