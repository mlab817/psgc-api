<?php

namespace App\Eloquent;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Municipality extends Model
{
    use SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'geographic_type', 'geographic_id', 'name', 'income_class', 'population', 'geometry', 'centroid'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'geographic_id'];

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
     * Collection of barangays under this municipality.
     */
    public function barangays()
    {
        return $this->morphMany(Barangay::class, 'geographic');
    }

    /**
     * Province or District that this municipality belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function geographic(): MorphTo
    {
        return $this->morphTo();
    }
}
