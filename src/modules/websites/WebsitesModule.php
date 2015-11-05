<?php

namespace P3in\Modules;

use Modular;
use P3in\Models\Navmenu;
use P3in\Models\Website;
use P3in\Module;
use P3in\Modules\BaseModule;
use P3in\Traits\NavigatableTrait as Navigatable;

Class WebsitesModule extends BaseModule
{

    use Navigatable;

    /**
     * Module Name
     */
	public $module_name = "websites";

	public function __construct() {}

    /**
     * Bootstrap, runs every time
     *
     */
	public function bootstrap()
	{
    }

    /**
     * Register, runs only on module load
     *
     */
    public function register()
    {
        $this->setConfig();

        Navmenu::byName('cp_main_nav')->addItem($this->navItem);

	}


    /**
     *
     */
    public function makeLink()
    {
      return [
        "label" => 'Websites Manager',
        "url" => '',
        "req_perms" => null,
        "props" => [
            'icon' => 'dashboard',
            "link" => [
                'data-click' => '/cp/websites',
                'data-target' => '#main-content'
            ],
        ]
      ];
    }


    public function setConfig()
    {

        $module = Modular::get($this->module_name);

        $module->config = [
            'base_url' => "/cp/{$this->module_name}",
            'index' => [
                'heading' => 'Manage Websites',
                'table' => [
                    'headers' => [
                        'Name',
                        'Site URL',
                        'Created',
                        'Updated',
                    ],
                    'rows' => [
                        'site_name' => [
                            'type' => 'link_by_id',
                            'target' => '#main-content',
                        ],
                        'site_url' => [
                            'type' => 'link_to_blank',
                        ],
                        'created_at' => [
                            'type' => 'datetime',
                        ],
                        'updated_at' => [
                            'type' => 'datetime',
                        ],
                    ],
                ],
            ],
            'show' => [
                'sub_section_name' => 'Website Configuration',
            ],
            'edit' => [
                'heading' => 'Connection Information',
                'route' => 'cp.websites.update'
            ],
            'create' => [
                'heading' => 'Create New Site',
                'route' => 'cp.websites.store'
            ],
            'form' => [
                'fields' => [
                    [
                        'label' => 'Name',
                        'name' => 'site_name',
                        'placeholder' => 'Website.com',
                        'type' => 'text',
                        'help_block' => '',
                    ],[
                        'label' => 'URL',
                        'name' => 'site_url',
                        'placeholder' => 'https://www.website.com',
                        'type' => 'text',
                        'help_block' => '',
                    ],[
                        'label' => 'From Email Address',
                        'name' => 'config[from_email]',
                        'placeholder' => 'website@website.com',
                        'type' => 'text',
                        'help_block' => '',
                    ],[
                        'label' => 'From Email Name',
                        'name' => 'config[from_name]',
                        'placeholder' => 'Website Name',
                        'type' => 'text',
                        'help_block' => '',
                    ],[
                        'label' => 'A Managed Website',
                        'name' => 'config[managed]',
                        'placeholder' => '',
                        'type' => 'checkbox',
                        'help_block' => '',
                    ],[
                        'label' => 'SSH Host',
                        'name' => 'config[ssh_host]',
                        'placeholder' => '127.0.0.1',
                        'type' => 'text',
                        'help_block' => '',
                    ],[
                        'label' => 'SSH Username',
                        'name' => 'config[ssh_username]',
                        'placeholder' => 'username',
                        'type' => 'text',
                        'help_block' => '',
                    ],[
                        'label' => 'SSH Password',
                        'name' => 'config[ssh_password]',
                        'placeholder' => 'password',
                        'type' => 'text',
                        'help_block' => 'Must use either SSH Password or SSH Key below (key is preferable).',
                    ],[
                        'label' => 'SSH Key',
                        'name' => 'config[ssh_key]',
                        'placeholder' => 'ssh_idrsa_key',
                        'type' => 'textarea',
                        'help_block' => 'Must use either SSH Key or SSH Password above (key is preferable).',
                    ],[
                        'label' => 'SSH Key Phrase',
                        'name' => 'config[ssh_keyphrase]',
                        'placeholder' => 'idrsa_key_passphrase',
                        'type' => 'text',
                        'help_block' => '',
                    ],[
                        'label' => 'Website Document Root',
                        'name' => 'config[ssh_root]',
                        'placeholder' => '/path/to/document/root',
                        'type' => 'text',
                        'help_block' => '',
                    ],
                ],
            ],
        ];

        $module->save();
    }

}