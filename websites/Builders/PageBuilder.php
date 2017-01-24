<?php

namespace P3in\Builders;

use Closure;
use Exception;
use P3in\Builders\PageLayoutBuilder;
use P3in\Models\Component;
use P3in\Models\Page;
use P3in\Traits\PublishesComponentsTrait;

class PageBuilder
{
    use PublishesComponentsTrait;

    /**
     * Page instance
     */
    private $page;
    private $website; // we need this to know where we store this page's template files.
    private $container;
    private $template = [];
    private $imports = [];

    public function __construct(Page $page = null)
    {
        if (!is_null($page)) {
            $this->page = $page;
            $this->website = $page->website;
        }

        return $this;
    }

    /**
     * new
     *
     * @param      Page  $page   The Page
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    public static function new($title, Website $website, Closure $closure = null)
    {
        $instance = new static();

        $instance->page = $website->pages()->create([

            'title' => $title,
        ]);

        if ($closure) {
            $closure($instance);
        }

        return $instance;
    }

    /**
     * edit
     *
     * @param      <type>       $page  The page being edited
     *
     * @throws     Exception   Page must be set
     *
     * @return     PageBuilder  PageBuilder instance
     */
    public static function edit($page)
    {
        if (!$page instanceof Page && !is_int($page)) {
            throw new Exception('Must pass id or page instance');
        }

        if (is_int($page)) {
            $page = Page::findOrFail($page);
        }

        return new static($page);
    }


    public function addChild($title, $slug)
    {
        $page = $this->page->createChild([
            'title' => $title,
            'slug' => $slug,
        ]);

        return new static($page);
    }

    /**
     * Add a Container to a page and return it's PageComponentContent model instance
     * since we will probably want to add sections to the container.
     * @param int $columns
     * @param int $order
     * @return PageBuilder PageBuilder instance
     */
    public function addContainer($columns = 1, $order = 0)
    {
        return $this->page->addContainer($columns, $order);
    }

    /**
     * Add a section to a container.
     * @param Component $component
     * @param int $columns
     * @param int $order
     * @return PageBuilder PageBuilder instance
     */
    public function addSection(Component $component, int $columns, int $order)
    {
        if ($this->container) {
            $this->container->addSection($component, $columns, $order);
            return $this;
        } else {
            throw new Exception('a Container must be set to add Sections.');
        }
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setAuthor($val = '')
    {
        return $this->setMeta('author', $val);
    }

    public function setDescription($val = '')
    {
        return $this->setMeta('description', $val);
    }

    public function setPriority($val = '')
    {
        return $this->setMeta('priority', $val);
    }

    public function setUpdatedFrequency($val = '')
    {
        return $this->setMeta('update_frequency', $val);
    }

    public function setMeta($key, $val)
    {
        $this->page->setMeta($key, $val);

        return $this;
    }


    private function buildPageTemplateTree($parts, $depth = 1)
    {
        $tab = str_pad('', $depth*2);

        foreach ($parts as $part) {
            $name = $part->component->template;

            if ($part->children->count() > 0) {
                $this->template[] = $tab.'div.col-'.$part->columns;

                $this->buildPageTemplateTree($part->children, $depth+1);
            } else {
                $this->template[] = $tab.$name;

                $import = "  import {$name} from './{$name}'";

                if (!in_array($import, $this->imports)) {
                    $this->imports[] = $import;
                }
            }
        }
    }

    public function compilePageTemplate()
    {
        $page = $this->page;
        $manager = $this->getMountManager();
        $name = $page->component_name;

        //@TODO: On delete or parent change of a page (we use the url as the unique name for a page),
        //we need to delete it's component file as to clean up junk.
        $this->buildPageTemplateTree($page->containers);

        $contents = '<template lang="jade">'."\n";
        $contents .= implode("\n", $this->template)."\n";
        $contents .= '</template>'."\n\n";
        $contents .= '<script>'."\n";
        $contents .= implode("\n", $this->imports)."\n";
        $contents .= '</script>'."\n";

        $manager->put('dest://' . $name.'.vue', $contents . "\n");
    }
}
