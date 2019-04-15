<?php

namespace AppCompass\AppCompass\Traits;

use Illuminate\Support\Facades\Cache;
use AppCompass\AppCompass\Models\Profiles\Profile;

trait HasProfileTrait
{
    // @TODO this is on vacation for the time being HIGH PRIORITY
    // public $profiles = [];
    // public function __construct($attributes = [])
    // {
    //     // we do this because profiles are loaded once on module install,
    //     // and we don't want to keep querying for the profiles on every request to
    //     // any class that uses the HasProfileTrait.
    //     $this->profiles = Cache::rememberForever('profile_types', function () {
    //         return Profile::get();
    //     });

    //     parent::__construct($attributes);
    // }

    // public function __call($name, $args)
    // {
    //     if ($profile = $this->profiles->where('name', $name)->first()) {
    //         return $this->hasOne($profile->class_name);
    //     } else {
    //         return parent::__call($name, $args);
    //     }
    // }

    // public function __get($name)
    // {
    //     if ($profile = $this->profiles->where('name', $name)->first()) {
    //         return $this->getRelationshipFromMethod($name);
    //     } else {
    //         return parent::__get($name);
    //     }
    // }
}
