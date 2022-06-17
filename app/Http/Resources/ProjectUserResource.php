<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $project = $this->project;

        return [
            'project_id'  => $this->project_id,
            'name'        => $project->name,
            'description' => $project->description,
            'status'      => $project->getStatus(),
            'close_at'    => $project->close_at,
            'created_at'  => $project->created_at,
            'total_users' => $project->project_users()->count()
        ];
    }
}
