<?php

namespace AppCompass\Interfaces;

use AppCompass\Models\MenuItem;

interface Linkable
{
    public function getTypeAttribute();

    public function makeMenuItem($order) : MenuItem;
}
