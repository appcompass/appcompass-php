<?php

namespace P3in\Interfaces;

use P3in\Models\MenuItem;

interface Linkable
{
    public function getTypeAttribute();

    public function makeMenuItem($order) : MenuItem;
}
