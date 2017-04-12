<?php

namespace P3in\Repositories;

use P3in\Models\Page;
use P3in\Models\PageSectionContent;
use P3in\Interfaces\PageContentRepositoryInterface;

class PageContentRepository extends AbstractChildRepository implements PageContentRepositoryInterface
{
    protected $view_types = ['PageEditor'];
    protected $rel_depth = 2;

    public function __construct(PageSectionContent $model, Page $parent)
    {
        $this->model = $model;

        $this->parent = $parent;

        $this->relationName = 'page';

        $this->parentToChild = 'contents';
    }
}