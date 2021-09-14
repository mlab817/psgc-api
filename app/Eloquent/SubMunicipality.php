<?php

namespace App\Eloquent;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class SubMunicipality extends Model
{
    use SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'city_id', 'name', 'population', 'geometry', 'centroid'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'city_id'];

    protected $spatialFields = ['geometry','centroid'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'code';
    }

    /**
     * Collection of barangays under this sub municipality.
     */
    public function barangays()
    {
        return $this->morphMany(Barangay::class, 'geographic');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
