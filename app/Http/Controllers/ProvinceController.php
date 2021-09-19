<?php

namespace App\Http\Controllers;

use App\Eloquent\Province;
use App\Http\Resources\ProvinceCollection;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\ProvinceResource;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $region = $request->query('region');

        $provinces = Province::query();

        if ($region) {
            $provinces->where('region_id', $region);
        }

        return new ProvinceCollection($provinces->get());

    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Province  $province
     */
    public function show(Request $request, Province $province)
    {
        $query = Province::where('id', $province->id);

        $province = QueryBuilder::for($query)
            ->allowedIncludes('cities', 'municipalities')
            ->first();

        return new ProvinceResource($province);
    }
}
