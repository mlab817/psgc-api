<?php

namespace App\Console\Commands;

use App\Eloquent\Barangay;
use App\Eloquent\Province;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Illuminate\Console\Command;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class ParseProvinceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:province';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert geometry data into provinces table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $shapeFile = new ShapefileReader(database_path('Provinces/provinces_psgc_geom.shp'));

            $bar = $this->output->createProgressBar($shapeFile->getTotRecords());

            while ($geometry = $shapeFile->fetchRecord()) {
                $province = Province::where('code', $geometry->getData('CODE'))->first();
                $geom = MultiPolygon::fromWKT($geometry->getWKT());

                if (! $province->geometry) {
                    $province->update([
                        'geometry' => $geom
                    ]);
                }
                $this->info('Inserted geometry to province: ', $province->name);
                $bar->advance();
            }

            $bar->finish();
            $this->info('Successfully inserted geometry');
        } catch (ShapefileException $e) {
            echo "Error Type: " . $e->getErrorType()
                . "\nMessage: " . $e->getMessage()
                . "\nDetails: " . $e->getDetails();
        }

        return 0;
    }
}
