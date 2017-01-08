<?php

namespace P3in\Models;

use Closure;
use Exception;
use Illuminate\Support\Facades\App;
use P3in\Models\Page;
use P3in\Models\PageSection;
use P3in\Models\Section;
use P3in\Models\Website;


/**
 * Examples:
 * new PageRenderer(Website::current())->render($url);
 */
class PageRenderer
{

    private $website;
    private $pages;
    private $page;
    private $build;

    public function __construct(Website $website)
    {
        $this->website = $website;
        $this->pages = $website->pages();


        return $this;
    }

    public function setPage($url)
    {
        if (is_string($url)) {

            $page = $this->getPageFromUrl($url);

        }else{
            throw new Exception('Must pass a url.');
        }

        $this->page = $page;

        return $this;
    }

    public function render()
    {

        if (!$this->page) {
            throw new Exception('A page must be set.');
        }

        $this->getSettings();
        $this->getContent();

        // structure the page data to be sent to the front-end to work out.
        return $this->build;
    }

    private function getSettings()
    {
        // fetch website settings that contain information on the website's header/footer/scripts/etc
        if ($settings = $this->website->settings) {
            $this->build['modules'] = $this->getModulesData($settings->modules);
        }else{
            throw new Exception('Website settings are not complete.');
        }
    }

    private function getModulesData($modulesSettings)
    {
        $rtn = [];
        foreach ($modulesSettings as $module_name => $settings) {
            $module = \Modular::get($module_name);
            $method = 'getRenderData';
            if (!empty($module->class_name) && method_exists($module->class_name, $method)) {
                $instance = App::make($module->class_name);
                $rtn[$module_name] = call_user_func_array([$instance, $method], [$settings]);
            }
        }
        return $rtn;
    }

    private function getContent()
    {
        // fetch and build the content of the page

        $this->build['content'] = []; //stuff
    }

    private function getPageFromUrl($url)
    {
        try {
            return $this->pages->byUrl($url)->firstorFail();
        } catch (Exception $e) {
            throw new Exception('There is no page by that URL.');
        }
    }

}