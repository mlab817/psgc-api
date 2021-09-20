<?php

namespace Database\Seeders;

use App\Eloquent\Province;
use App\Eloquent\Region;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $files = File::allFiles(base_path('geojson/provinces/medres'));

        $errors = [];

        foreach ($files as $file) {
            $json = json_decode(file_get_contents($file));

            $features = $json->features;

            foreach ($features as $feature) {
                $geometry = null;

                if ($feature->geometry->type == 'MultiPolygon') {
                    $geometry = MultiPolygon::fromJson(json_encode($feature->geometry));
                } else {
                    $geometry = Polygon::fromJson(json_encode($feature->geometry));
                }

                $code = str_replace('PH', '', $feature->properties->ADM2_PCODE);

                try {
                    $province = Province::where('code', $code)->firstOrFail();

                    $province->update([
                        //                    'geometry' => $geometry,
                        'geometry' => MultiPolygon::fromWKT($geometry->toWKT()),
                    ]);
                } catch (ModelNotFoundException $exception) {
                    array_push($errors, $exception->getMessage() . ': ' . $feature->properties->ADM2_EN);
                }
                echo $feature->properties->ADM2_EN;
            }
        }

        foreach ($errors as $error) {
            echo $error . '\n';
        }
    }
}
