<?php

namespace AppCompass\AppCompass\Interfaces;

use AppCompass\AppCompass\Models\MenuItem;

interface Linkable
{
    public function getTypeAttribute();

    public function makeMenuItem($order) : MenuItem;
}
