<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RegionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type'      => 'FeatureCollection',
            'features'  => $this->collection,
            'notes'     => 'This map is based on the 2011 map provided in https://github.com/faeldon/philippines-json-maps/blob/master/2011/geojson/regions/medres/regions.0.01.json. This was before the new NIR was created which has been reversed since.',
            'source'    => 'https://github.com/faeldon/philippines-json-maps/blob/master/2011/geojson/regions/medres/regions.0.01.json'
        ];
    }
}
