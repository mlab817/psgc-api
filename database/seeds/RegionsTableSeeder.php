<?php

namespace Database\Seeders;

use App\Eloquent\Region;
use GeoJson\Feature\Feature;
use Grimzy\LaravelMysqlSpatial\Types\Geometry;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Mul;

class RegionsTableSeeder extends DatabaseSeeder
{
    public function remap($regionName)
    {
        $regions = [
            "Autonomous Region of Muslim Mindanao (ARMM)" => 'Autonomous Region In Muslim Mindanao (ARMM)',
            "Bicol Region (Region V)" => 'Region V (Bicol Region)',
            "CALABARZON (Region IV-A)" => 'Region IV-A (CALABARZON)',
            "Cagayan Valley (Region II)" => 'Region II (Cagayan Valley)',
            "Caraga (Region XIII)" => 'Region XIII (Caraga)',
            "Central Luzon (Region III)" => 'Region III (Central Luzon)',
            "Central Visayas (Region VII)" => 'Region VII (Central Visayas)',
            "Davao Region (Region XI)" => 'Region XI (Davao Region)',
            "Eastern Visayas (Region VIII)" => 'Region VIII (Eastern Visayas)',
            "Ilocos Region (Region I)" => 'Region I (Ilocos Region)',
            "MIMAROPA (Region IV-B)" => 'MIMAROPA Region',
            "Metropolitan Manila" => 'National Capital Region (NCR)',
            "Northern Mindanao (Region X)" => 'Region X (Northern Mindanao)',
            "SOCCSKSARGEN (Region XII)" => 'Region XII (SOCCSKSARGEN)',
            "Western Visayas (Region VI)" => 'Region VI (Western Visayas)',
            "Zamboanga Peninsula (Region IX)" => 'Region IX (Zamboanga Peninsula)',
        ];

        if (in_array($regionName, array_keys($regions))) {
            return $regions[$regionName];
        }

        return $regionName;
    }

    /**
     *
     */
    public function run()
    {
        $regionJson = json_decode(file_get_contents(base_path('geojson/regions/medres/regions.0.01.json')));

        $features = $regionJson->features;

        $errors = [];
        foreach ($features as $feature) {
            $geometry = null;

            if ($feature->geometry->type == 'MultiPolygon') {
                $geometry = MultiPolygon::fromJson(json_encode($feature->geometry));
            } else {
                $geometry = Polygon::fromJson(json_encode($feature->geometry));
            }

            $code = str_replace('PH', '', $feature->properties->REGION);

            try {
                $region = Region::where('name', $this->remap($code))->firstOrFail();

                $region->update([
//                    'geometry' => $geometry,
                    'geometry' => MultiPolygon::fromWKT($geometry->toWKT()),
                ]);
            } catch (ModelNotFoundException $exception) {
                array_push($errors, $exception->getMessage() . ': ' . $code);
            }
        }
        dd($errors);
    }
}
