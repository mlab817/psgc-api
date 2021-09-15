<?php

namespace App\Console\Commands;

use App\Eloquent\Province;
use App\Eloquent\Region;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Console\Command;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class ParseRegionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:regions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add geometry and centroid to regions table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->parseGeometry();

        $this->parseCentroid();

        return 0;
    }

    public function getRegion(string $code)
    {
        return Region::where('code', $code)->first();
    }

    public function parseGeometry()
    {
        try {
            $shapeFile = new ShapefileReader(database_path('Regions/regions geom.shp'));

            $bar = $this->output->createProgressBar($shapeFile->getTotRecords());

            while ($geometry = $shapeFile->fetchRecord()) {
                $region = $this->getRegion((string) $geometry->getData('CODE'));
                $geom = MultiPolygon::fromWKT($geometry->getWKT());

                if (! $region->geometry) {
                    $region->update([
                        'geometry' => $geom
                    ]);
                }
                $this->info('Inserted geometry to Region: ', $region->name);
                $bar->advance();
            }

            $bar->finish();
            $this->info('Successfully inserted geometry');
        } catch (ShapefileException $e) {
            echo "Error Type: " . $e->getErrorType()
                . "\nMessage: " . $e->getMessage()
                . "\nDetails: " . $e->getDetails();
        }
    }

    public function parseCentroid()
    {
        try {
            $shapeFile = new ShapefileReader(database_path('Regions/regions centroid.shp'));

            $bar = $this->output->createProgressBar($shapeFile->getTotRecords());

            while ($geometry = $shapeFile->fetchRecord()) {
                $region = $this->getRegion((string) $geometry->getData('CODE'));
                $geom = Point::fromWKT($geometry->getWKT());

                if (! $region->centroid) {
                    $region->update([
                        'centroid' => $geom
                    ]);
                }
                $this->info('Inserted geometry to Region: ', $region->name);
                $bar->advance();
            }

            $bar->finish();
            $this->info('Successfully inserted geometry');
        } catch (ShapefileException $e) {
            echo "Error Type: " . $e->getErrorType()
                . "\nMessage: " . $e->getMessage()
                . "\nDetails: " . $e->getDetails();
        }
    }
}
