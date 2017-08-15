<?php

namespace P3in\Builders;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use P3in\Models\Field;
use P3in\Models\Form;
use P3in\Models\Resource;

class FormBuilder
{

    /**
     * Form instance
     */
    public $form;

    /**
     * This field will be the parent of every field added
     */
    protected $parent = null;

    private function __construct(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * New up a FormBuilder
     *
     * @param      string $form The form's name
     *
     * @return     FormBuilder
     */
    public static function new($name, Closure $closure = null) : FormBuilder
    {
        // @NOTE new form means new form. old is deleted.
        FormBuilder::seekAndDestroy($name);

        $form = Form::create([
            'name' => $name,
        ]);

        $instance = new static($form);

        if ($closure) {
            $closure($instance);
        }

        return $instance;
    }

    /**
     * Edit a form
     *
     * @param      string $form The form
     *
     * @return     static  ( description_of_the_return_value )
     */
    public static function edit($form, Closure $closure = null) : FormBuilder
    {
        if ($form instanceof Form) {
            $instance = new static($form);
        } elseif (is_string($form)) {
            $instance = new static(Form::whereName($form)->firstOrFail());
        } elseif (is_integer($form)) {
            $instance = new static(Form::findOrFail($form));
        }

        if ($closure) {
            $closure($instance);
        }

        return $instance;
    }

    /**
     * Gets the form.
     *
     * @return     <type>  The form.
     */
    public function getForm() : Form
    {
        return $this->form;
    }

    /**
     * Sets the owner.
     *
     * @param      Model $owner The owner
     */
    public function setOwner(Model $owner)
    {
        $this->form->setOwner($owner);

        return $this;
    }


    public function linkToResource($resource, $permission = null)
    {
        if (is_string($resource)) {
            $record = Resource::firstOrNew([
                'resource' => $resource,
            ]);
        } elseif ($resource instanceof Resource) {
            $record = $resource;
        } else {
            return false;
        }

        $record->form()->associate($this->form);

        if ($permission) {
            $record->setPermission($permission);
        }
        $record->save();

        return $this;
    }

    /**
     * Links to resources.
     *
     * @param      Mixed  $resources  The resources you're linking the form to
     * @param      String $permission The default permission to assign to each resource.
     */
    public function linkToResources($resources, $permission = null)
    {
        foreach ($resources as $resource) {
            $this->linkToResource($resource, $permission);
        }

        return $this;
    }

    /**
     * sets the editor view
     *
     * @param      <type>  $editor  The editor
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function editor($editor)
    {
        $this->form->editor($editor);

        return $this;
    }

    /**
     * Gets the name.
     *
     * @param      <type>  $name   The name
     *
     * @return     <type>  The name.
     */
    public function getName($name = null)
    {
        return $this->form->name;
    }

    /**
     * Sets the name.
     *
     * @param      <type>  $name   The name
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function setName($name)
    {
        $this->form->update(['name' => $name]);

        return $this;
    }

    public function setConfig($key, $name = null)
    {
        $this->form->setConfig($key, $name);

        return $this;
    }

    /**
     * Set a Parent Field for subsequent fields
     *
     * @param      <type>  $parent  The parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Gets the fields.
     *
     * @return     <type>  The fields.
     */
    public function getFields()
    {
        return $this->form->fields;
    }

    /**
     * drops a field by name or name/type
     *
     * @param      <string>      $name   The name
     * @param      <string>      $type   The type
     *
     * @throws     Exception    (in case no single combination is found)
     *
     * @return     self          ( self on success/no match )
     */
    public function drop($name, $type = null)
    {
        $field = $this->form->fields->where('name', $name);

        if (!count($field)) {
            return $this;
        }

        if (count($field) > 1 and is_null($type)) {
            throw new Exception("Multiple <{$name}> found, please add type");
        } elseif (count($field) > 1 and !is_null($type)) {
            $field = $field->where('type', $type);

            if (count($field) > 1) {
                throw new Exception("Sorry there doesn't seem to be an enough specific combination to get a single result. Halting.");
            } else {
                $field->first()->delete();

                return $this;
            }
        } elseif (count($field) === 1) {
            $field->first()->delete();
        }
    }

    /**
     * Try to match FieldTypes
     *
     * @param      <type>  $field_type  The field_type we are trying to add
     * @param      array $args [0] => Field Name [1] => Field Maps To
     *
     * @return     $this->addField
     */
    public function __call($field_type, array $args)
    {

        // @TODO before calling for types we can simplify this class and check if $method can be called on the form? -f

        // setup class name
        $field_name = ucfirst($field_type) . 'Type';

        // full class name
        // @TODO: too vague, should be P3in\Models\FieldTypes\WhateverType
        $class_name = '\P3in\Models\FieldTypes\\' . $field_name;

        // if no such class we dead
        if (!class_exists($class_name)) {
            die("The FieldType: <$field_name> does not exist. Do Something!\n");
        }

        // if class exists makes a field instance
        $field_type = $class_name::make($args[0], $args[1], $this->form);

        // associate a parent (in case of sub-forms)
        if (!is_null($this->parent)) {
            $field_type->field->setParent($this->parent);
        }

        // handle a nested form
        if (isset($args[2])) {
            if (is_object($args[2]) && get_class($args[2]) === 'Closure') {

                // formbuilder instance with the correct parent set
                $fb = FormBuilder::edit($this->form->id)->setParent($field_type->field);

                $args[2]($fb);
            } else {
                $this->parent = null;
            }
        }

        return $field_type->field;
    }

    /**
     *
     */
    private static function seekAndDestroy($name)
    {
        try {
            $form = Form::whereName($name)->firstOrFail();

            $form->delete();
        } catch (ModelNotFoundException $e) {
            return;
        }
    }
}
