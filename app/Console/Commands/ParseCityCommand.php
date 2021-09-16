<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Illuminate\Console\Command;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class ParseCityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function getCity(string $code)
    {
        return City::where('code', $code)->first();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->parseGeometry();

        return 0;
    }

    public function parseGeometry()
    {
        try {
            $shapeFile = new ShapefileReader(database_path('Cities/cities.shp'));

            $bar = $this->output->createProgressBar($shapeFile->getTotRecords());

            while ($geometry = $shapeFile->fetchRecord()) {
                $city = $this->getCity((string) $geometry->getData('CODE'));
                if (! $city) {
                    continue;
                }
                $geom = MultiPolygon::fromWKT($geometry->getWKT());

                $city->update([
                    'geometry' => $geom
                ]);

                $this->info('Inserted geometry to City: ', $city->name);
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
