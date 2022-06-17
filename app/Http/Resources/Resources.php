<?php

namespace App\Http\Resources;

final class Resources
{
    /**
     * Instace of resources
     */
    private static $resources = [
        'project_user' => \App\Http\Resources\ProjectUserResource::class,
    ];

    /**
     * Transform the collections into an array.
     */
    public static function collection($resourceName, $collections)
    {
        $resource = self::$resources[$resourceName];
        return $resource::collection($collections);
    }
}
