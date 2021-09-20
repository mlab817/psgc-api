<?php

namespace App\Http\Controllers;

use App\Eloquent\Region;
use App\Http\Resources\RegionCollection;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\RegionResource;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        return new RegionCollection(Region::all());
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Region  $region
     */
    public function show(Request $request, Region $region)
    {
        $query = Region::where('id', $region->id);

        $region = QueryBuilder::for($query)
            ->allowedIncludes('provinces', 'districts')
            ->first();

        return new RegionResource($region);
    }
}
