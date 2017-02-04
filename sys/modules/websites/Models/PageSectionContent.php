<?php

namespace P3in\Models;

use Illuminate\Database\Eloquent\Model;
use P3in\Models\Section;
use P3in\Models\Page;
use Exception;

class PageSectionContent extends Model
{
    protected $table = 'page_section_content';

    protected $fillable = [
        'config',
        'order',
        'content',
    ];

    protected $casts = [
        'config' => 'object',
        'content' => 'object',
    ];

    // protected $with = ['section'];

    /**
     *
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     *
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * parent
     *
     * @return     BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * children
     *
     * @return     HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // /**
    //  *
    //  */
    // public function photos()
    // {
    //     return $this->morphMany(Photo::class, 'photoable');
    // }

    public function source()
    {
        return $this->morphOne(\P3in\Models\FieldSource::class, 'linked');
    }

    /**
     * Gets the content attribute.
     *
     * @return     <type>  The content attribute.
     */
    public function getContentAttribute()
    {
        if ($this->source) {

            $content = json_decode($this->attributes['content']);

            $content->{$this->source->related_field} = $this->source->toArray();

            return json_encode($content);

        } else {

            return $this->attributes['content'];

        }
    }

    /**
     * Set the current section to a container type.
     * @param int $order
     * @param array $config
     * @return Model PageSectionContent
     */
    public function saveAsContainer(int $order, array $config = null)
    {
        $container = Section::getContainer();

        $this->fill(['order' => $order, 'config' => $config]);

        $this->section()->associate($container);

        $this->save();

        return $this;
    }

    /**
     * Add a child container to a parent.
     * @param int $order
     * @param array $config
     * @return Model PageSectionContent
     */
    public function addContainer(int $order, array $config = null)
    {
        $container = Section::getContainer();

        return $this->addSection($container, $order, [
            'config' => $config
        ], true);
    }

    /**
     * Add a section to a container.
     *
     * @param      Section    $section
     * @param      int        $order
     * @param      array      $data         The data
     * @param      boolean    $returnChild  The return child
     * @param      int   $columns
     *
     * @throws     Exception  (description)
     *
     * @return     Model      PageSectionContent
     */
    public function addSection(Section $section, int $order, array $data = [], $returnChild = false)
    {
        if (!$this->isContainer()) {

            throw new Exception('a Section can only be added to a Container.');

        }

        $data = array_merge(['order' => $order], $data);

        if (isset($data['content'])) {

            // @TODO validate the structure.  Throw error if doesn't match the section form structure and rules.

        } else {

            $data['content'] = [];

        }

        $child = new self($data);

        $child->section()->associate($section);

        $child->page()->associate($this->page);

        $this->children()->save($child);

        if ($returnChild) {

            return $child;

        }

        return $this;
    }

    public function dynamic($source, $callback)
    {
        $field_source = FieldSource::create([
            'linked_id' => $this->id,
            'linked_type' => get_class($this),
            'data' => [],
            'criteria' => []
        ]);

        if (is_string($source) && class_exists($source)) {

            $field_source->sourceable_type = $source;

        } elseif ($source instanceof Model) {

            $field_source->sourceable_type = $source;

            if (isset($source->{$source->getKeyName()}) && !is_null($source->{$source->getKeyName()}) ) {

                $field_source->sourceable_id = $source->{$source->getKeyName()};

            }

        }

        if ($callback) {

            $callback($field_source);

        }

        return $this;
    }

    /**
     * Saves a content.
     *
     * @param      <type>  $content  The content
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function saveContent($content)
    {
        $this->content = $content;
        $this->save();
        return $this;
    }

    /**
     * Builds the props for an element
     * @return array of strings
     */
    public function buildProps()
    {
        $props = [];
        $conf = $this->config;
        if (!empty($conf->props) && is_object($conf->props)) {
            foreach ($conf->props as $key => $val) {
                // creates this: :to="from"
                $props[] = ':'.$key.'="'.$val.'"';
            }
        }
        return $props;
    }
    /**
     * Determines if container.
     *
     * @return     boolean  True if container, False otherwise.
     */
    public function isContainer()
    {
        return $this->section->type === 'container';
    }
}
