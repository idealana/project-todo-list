<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Resources;

class ProjectUserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'added_by' => $this->when(
                $this->relationLoaded('user_input'),
                Resources::new('user', $this->user_input)
            ),

            'user' => $this->when(
                $this->relationLoaded('user'),
                Resources::new('user', $this->user)
            ),

            'todo_lists' => $this->when(
                $this->relationLoaded('project_todo_lists'),
                Resources::collection('user_todo_list', $this->project_todo_lists)
            ),
        ];
    }
}
