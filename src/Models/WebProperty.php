<?php

namespace P3in\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class WebProperty extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'scheme',
        'host',
    ];

    /**
     *
     */
    public $appends = ['url'];


    /**
     * Menus
     *
     * @return     hasMany
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Menus
     *
     * @return     hasMany
     */
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    /**
     * Gets the url attribute.
     *
     * @return     <type>  The url attribute.
     */
    public function getUrlAttribute()
    {
        return $this->attributes['scheme'] . '://' . $this->attributes['host'];
    }

    public static function fromRequest(Request $request, $host = null)
    {
        $host = $host ?? $request->header('Site-Host');
        try {
            return self::whereHost($host)->firstOrFail();
        } catch (NotFoundException $e) {
            app()->abort(401, $host . ' Not Authorized');
        } catch (ModelNotFoundException $e) {
            app()->abort(401, $host . ' Not Authorized');
        }
    }

    /**
     * builds router.
     *
     * @return     <array>  vue router structured array.
     */
    public function buildRoutesTree()
    {
        $resources = $this->resources()->byConfig('layout', '!=', '')
            ->byAllowed()
            ->get();

        $rtn = [];
        foreach ($resources->unique('config.layout')->pluck('config.layout') as $layout) {
            if ($layout) {
                $rtn[] = [
                    'path'      => '',
                    'component' => $layout,
                    'children'  => $this->formatRoutesBranch($resources->where('config.layout', $layout)),
                ];
            }
        }

        return $rtn;
    }

    private function formatRoutesBranch($resources)
    {
        $rtn = [];
        foreach ($resources as $resource) {
            $rtn[] = $resource->vueRoute();
        }

        return $rtn;
    }
}
