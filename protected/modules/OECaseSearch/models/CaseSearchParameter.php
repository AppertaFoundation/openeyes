<?php

/**
 * Class CaseSearchParameter
 */
abstract class CaseSearchParameter extends CFormModel
{
    /**
     * @var string $name
     */
    public $name;

    /**
     * @var bool|string $operation
     */
    public $operation;

    /**
     * @var integer $id
     */
    public $id;

    /**
     * @var bool $isFixed
     */
    public $isFixed = false;

    /**
     * Get the parameter identifier (usually the name).
     * @return string The human-readable name of the parameter (for display purposes).
     */
    abstract public function getLabel();

    /**
     * Get the path of the view to use when rendering the search parameter. Override this function if the parameter is within a different module.
     * By default, the view name is just the class name.
     * @return string
     */
    public function getViewPath()
    {
        return get_class($this);
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return string[] An array of attribute names.
     */
    public function attributeNames()
    {
        return array('name', 'operation', 'id');
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array(
            array('id, operation, name', 'safe'),
        );
    }

    /**
     * Override this function to customise the output within the audit table. Generally it should be something like "name: < val".
     * @return string|null The audit string.
     */
    public function getAuditData()
    {
        return null;
    }

    public function getDisplayTitle()
    {
        return $this->getLabel();
    }

    /**
     * Returns an array representing the parameter to store in the database. This array will be serialised before storage.
     * Subclasses should override this method and extend the array.
     * @return array
     */
    public function saveSearch()
    {
        return array(
            'class_name' => get_class($this),
            'name' => $this->name,
            'operation' => $this->operation,
            'id' => $this->id,
            'isFixed' => $this->isFixed,
        );
    }

    /**
     * Load the specified array into the parameter. This should follow the same convention as how the parameter was saved.
     * @param $serialised_data array
     */
    public function loadSearch($serialised_data)
    {
        foreach ($serialised_data as $property => $value) {
            if ($property !== 'class_name') {
                $this->$property = $value;
            }
        }
    }

    abstract public function getDisplayString();
}
