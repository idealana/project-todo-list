<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id', 'name', 'description', 'finished_at' ];

    public function isFinished()
    {
    	return ! empty($this->finished_at);
    }

    public function setFinished()
    {
    	$this->finished_at = now();
    	return $this;
    }

    public function setUnfinished()
    {
    	$this->finished_at = null;
    	return $this;
    }
}
