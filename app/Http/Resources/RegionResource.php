<?php

namespace App\Http\Resources;

use Grimzy\LaravelMysqlSpatial\Types\Geometry;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'properties' => [
                'code'      => $this->code,
                'name'      => $this->name,
                'population'=> $this->population,
            ],
            'type'      => 'Feature',
            'geometry'  => $this->geometry,
        ];
    }
}
