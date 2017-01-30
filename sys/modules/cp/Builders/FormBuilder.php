<?php

namespace P3in\Builders;

use Closure;
use Illuminate\Database\Eloquent\Model;
use P3in\Models\Field;
use P3in\Models\Form;
use P3in\Models\Resource;
use P3in\Models\Types\BaseField;

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
     * @param      string  $form   The form's name
     *
     * @return     FormBuilder
     */
    public static function new($name, Closure $closure = null): FormBuilder
    {
        $form = Form::firstOrCreate([
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
     * @param      string  $form   The form
     *
     * @return     static  ( description_of_the_return_value )
     */
    public static function edit($form): FormBuilder
    {
        if ($form instanceof Form) {

            $instance = new static($form);

        } elseif (is_string($form)) {

            $instance = new static(Form::whereName($form)->firstOrFail());

        } else if (is_integer($form)) {

            $instance = new static(Form::findOrFail($form));

        }

        return $instance;
    }

    /**
     * Gets the form.
     *
     * @return     <type>  The form.
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * Sets the owner.
     *
     * @param      Model  $owner  The owner
     */
    public function setOwner(Model $owner)
    {
        $this->form->setOwner($owner);

        return $this;
    }

    /**
     * Links to resources.
     *
     * @param      Mixed  $resources  The resources you're linking the form to
     */
    public function linkToResources($resources)
    {
        foreach ((array) $resources as $resource) {
            Resource::create([
                'resource' => $resource,
                'form_id' => $this->form->id
            ]);
        }

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
     * Adds a field from a BaseField.
     *
     */
    private function addField(BaseField $field_type): Field
    {
        $this->form->fields()->attach($field_type->field);

        return $field_type->field;
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
     * @param      array   $args    [0] => Field Name [1] => Field Maps To
     *
     * @return     $this->addField
     */
    public function __call($field_type, array $args)
    {
        // we'll try to new ucfirst that class, if that works we got it already
        $field_name = ucfirst($field_type) . 'Type';

        // full class name
        $class_name = '\P3in\Models\Types\\' . $field_name;

        if (!class_exists($class_name)) {

            die("The FieldType: <$field_name> does not exist. Do Something!\n");

        }

        // Field instance
        $field_type = $class_name::make($args[0], $args[1]);

        $field = $this->addField($class_name::make($args[0], $args[1]));

        if (!is_null($this->parent)) {

            $field->setParent($this->parent);

        }

        if (isset($args[2])) {


            if (is_object($args[2]) && get_class($args[2]) === 'Closure') {


                $fb = FormBuilder::edit($this->form->id)->setParent($field);


                $args[2]($fb);

            }

        }

        return $field;

    }
}
