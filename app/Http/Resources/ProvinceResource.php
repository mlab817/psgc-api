<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProvinceResource extends JsonResource
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
        $type = $request->type ? $request->type : 'geometry';
//        $condition = $request->include != 'provinces' && ! is_null($request->province) && is_null($request->city) && is_null($request->municipality);

        return [
            'id'            => $this->id,
            'type'          => 'Feature',
            'properties'    => [
                'code'           => $this->code,
                'name'           => $this->name,
                'income_class'   => $this->income_class,
                'population'     => $this->population,
            ],
            'geometry'      => $type == 'centroid' ? $this->centroid : $this->geometry,
//            'region'         => $this->when($condition, new RegionResource($this->region)),
//            'cities'         => CityResource::collection($this->whenLoaded('cities')),
//            'municipalities' => MunicipalityResource::collection($this->whenLoaded('municipalities')),
        ];
    }
}
