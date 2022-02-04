<?php

/**
 * Base class for all parameters usable in Advanced Search.
 * @property string $label
 */

abstract class CaseSearchParameter extends CFormModel
{
    protected const _AUTOCOMPLETE_LIMIT = 30;
    /**
     * @var string $name Internal name of the parameter.
     */
    public string $name;

    /**
     * @var bool|string $operation The operator to use in searching.
     */
    public $operation;

    /**
     * @var string|int $id Internal unique ID assigned to the parameter instance. This allows repeating parameters.
     */
    public int $id;

    /**
     * @var string|int|null $value Parameter value.
     */
    public $value;

    /**
     * @var string|null $label Label to display in adder dialog for the parameter.
     */
    protected string $label_ = '';

    /**
     * @var bool $isSaved Indicates whether the parameter should be saved when saving a search.
     */
    public bool $isSaved = true;

    /**
     * @var string[] $options List of options for the Adder Dialog.
     */
    protected array $options = array(
        'value_type' => 'string',
        'operations' => array(
            array('label' => 'IS', 'id' => '='),
            array('label' => 'IS NOT', 'id' => '!='),
        )
    );

    /**
     * Get the parameter label. This is generally used implicitly in a magic method.
     * @return string The human-readable name of the parameter (for display purposes).
     */
    final public function getLabel(): string
    {
        return $this->label_;
    }

    /**
     * Override this function if the parameter subclass has extra validation rules.
     * If doing so, ensure you invoke the parent function first to
     * obtain the initial list of rules and merge the arrays together.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array(
            array('id, operation, name', 'required'),
            array('id, operation, name, value', 'safe'),
        );
    }

    /**
     * Get the list of attribute labels.
     * @return array List of attribute labels.
     * @uses CaseSearchParameter::getLabel()
     */
    public function attributeLabels()
    {
        return array(
            'operation' => $this->label . ' parameter operator',
            'value' => $this->label,
        );
    }

    /**
     * Display the user-friendly representation of the specified parameter attribute.
     * Override this function to customise the value returned for specific attributes.
     * If the parameter does not exist, an exception is thrown.
     * @param $attribute string The attribute name
     * @return mixed|void The representation of the attribute value.
     * @throws CException Attribute does not exist.
     */
    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            return $this->$attribute;
        }
        throw new CException('Attribute does not exist.');
    }

    /**
     * Override this function to customise the output within the audit table.
     * Generally it should be something like "name: < val".
     * @return string The audit string.
     */
    public function getAuditData(): string
    {
        return $this->name;
    }

    /**
     * Returns an array representing the parameter to store in the database.
     * This array will be serialised before storage.
     * Subclasses should override this method and extend the array with any extra properties that need to be saved.
     * @return array
     */
    public function saveSearch(): array
    {
        return array(
            'class_name' => get_class($this),
            'name' => $this->name,
            'operation' => $this->operation,
            'id' => $this->id,
            'value' => $this->value
        );
    }

    /**
     * Load the specified array into the parameter.
     * This should follow the same convention as how the parameter was saved.
     * @param $serialised_data array
     */
    public function loadSearch(array $serialised_data): void
    {
        foreach ($serialised_data as $property => $value) {
            if ($property !== 'class_name') {
                $this->$property = $value;
            }
        }
    }

    /**
     * Get the list of adder dialog options
     * @return string[] List of adder dialog options.
     */
    final public function getOptions(): array
    {
        return $this->options;
    }
}
