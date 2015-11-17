<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth;
use Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use P3in\Controllers\ModularBaseController;
use P3in\Models\Navmenu;
use P3in\Models\Website;

class UiBaseController extends ModularBaseController {

    public $records;
    public $record;
    public $site_url;

    public function build($type, $root = [])
    {
        $method = 'view'.$type;

        $this->setBaseUrl($root);

        if (!isset($this->meta->data_target)) {

            $this->setDataTarget($root);

        }

        return call_user_func([$this, $method]);
    }

    private function viewIndex()
    {
        return view('ui::index', [
            'meta' => $this->meta,
            'records' => $this->records,
        ]);
    }

    private function viewCreate()
    {
        return view('ui::create', [
            'meta' => $this->meta,
        ]);
    }

    private function viewShow()
    {
        return view('ui::show', [
            'meta' => $this->meta,
            'record' => $this->record,
            'nav' => $this->getCpSubNav(),
            'left_panels' => $this->getLeftPanels(),
        ]);

    }

    private function viewEdit()
    {
        return view('ui::edit', [
            'meta' => $this->meta,
            'record' => $this->record,
        ]);
    }

    /**
     *
     */
    public function getCpSubNav($id = null)
    {

        if (!is_null($id)) {

            // dd($id);

        }

        $navmenu_name = 'cp_'.$this->module_name.'_subnav';

        $navmenu = Navmenu::byName($navmenu_name);

        return $navmenu;

    }

    /**
     *  SetBaseUrl
     *
     */
    public function setBaseUrl($root)
    {

        $this->meta->base_url = '/'.implode('/', $root);

        return $this->meta->base_url;

    }

    /**
     *
     */
    public function setDataTarget($root)
    {

        if (count($root) == 1 && !isset($this->meta->data_target)) {

            $this->meta->data_target = '#main-content-out';

        } else {

            $this->meta->data_target = '#record-detail';

        }

        return $this->meta->data_target;

    }

    /**
     *
     */
    public function getLeftPanels() {}

}