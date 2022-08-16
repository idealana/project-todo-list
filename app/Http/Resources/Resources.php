<?php

namespace App\Http\Resources;

final class Resources
{
    /**
     * Instace of resources
     */
    private static $resources = [
        'project' => \App\Http\Resources\ProjectResource::class,
        'project_user' => \App\Http\Resources\ProjectUserResource::class,
        'project_user_lists' => \App\Http\Resources\ProjectUserListResource::class,
        'user' => \App\Http\Resources\UserResource::class,
        'user_todo_list' => \App\Http\Resources\ProjectTodoListResource::class,
    ];

    /**
     * Transform the collections into an array.
     */
    public static function collection($resourceName, $collections)
    {
        $resource = self::$resources[$resourceName];
        return $resource::collection($collections);
    }

    /**
     * Transform the collection into an array.
     */
    public static function new($resourceName, $collection)
    {
        $resource = self::$resources[$resourceName];
        return new $resource($collection);
    }
}
