<?php

namespace P3in\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Intervention\Image\Exception\NotFoundException;

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
}
