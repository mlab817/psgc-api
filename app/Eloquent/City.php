<?php

namespace App\Eloquent;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class City extends Model
{
    use SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'geographic_type', 'geographic_id', 'name', 'city_class', 'income_class', 'population', 'geometry', 'centroid'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'geographic_type', 'geographic_id'];

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
     * Province or District that this city belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function geographic(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Collection of sub municipalities under this city.
     */
    public function subMunicipalities(): HasMany
    {
        return $this->hasMany(SubMunicipality::class);
    }

    /**
     * Collection of barangays under this city.
     */
    public function barangays(): MorphMany
    {
        return $this->morphMany(Barangay::class, 'geographic');
    }
}
