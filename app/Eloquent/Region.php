<?php

namespace App\Eloquent;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'population', 'geometry', 'centroid'];

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

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
