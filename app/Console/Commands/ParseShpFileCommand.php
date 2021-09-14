<?php

namespace App\Console\Commands;

use App\Eloquent\Barangay;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class ParseShpFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:shp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse shape file and upload to db';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $shapeFile = new ShapefileReader(database_path('bgy map.shp'));
            $bar = $this->output->createProgressBar($shapeFile->getTotRecords());
            while ($geometry = $shapeFile->fetchRecord()) {
                $barangay = Barangay::where('code', $geometry->getData('CODE'))->first();
                $geom = MultiPolygon::fromWKT($geometry->getWKT());

                $barangay->update([
                    'geometry' => $geom
                ]);
                $this->info('Inserted geometry to barangay: ', $barangay->name);
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
