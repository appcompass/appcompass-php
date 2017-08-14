<?php

$module_class_name = "\P3in\AppCompassModule";

$dependencies = [];

if (isset($path)) {
    require_once($path . '/AppCompassModule.php');

    return $module_class_name::makeInstance($path);
}

throw new \Exception('Path not specified while trying to load <app-compass> module.');
