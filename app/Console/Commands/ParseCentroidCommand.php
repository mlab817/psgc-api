<?php

namespace App\Console\Commands;

use App\Eloquent\Province;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Console\Command;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class ParseCentroidCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:centroid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $shapeFile = new ShapefileReader(database_path('Provinces/provinces_psgc_centroid.shp'));

            $bar = $this->output->createProgressBar($shapeFile->getTotRecords());

            while ($geometry = $shapeFile->fetchRecord()) {
                $province = Province::where('code', $geometry->getData('CODE'))->first();
                $centroid = Point::fromWKT($geometry->getWKT());

                if (! $province->centroid) {
                    $province->update([
                        'centroid' => $centroid
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
