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
    public $name;

    /**
     * @var bool|string $operation The operator to use in searching.
     */
    public $operation;

    /**
     * @var int $id Internal unique ID assigned to the parameter instance. This allows repeating parameters.
     */
    public $id;

    /**
     * @var string|int|bool $value Parameter value.
     */
    public $value;

    /**
     * @var string $label Label to display in adder dialog for the parameter.
     */
    protected $label_ = null;

    /**
     * @var string[] $options List of options for the Adder Dialog.
     */
    protected $options = array(
        'value_type' => 'string',
    );

    /**
     * CaseSearchParameter constructor.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        if (!array_key_exists('operations', $this->options)) {
            $this->options['operations'] = array(
                array('label' => 'IS', 'id' => '='),
                array('label' => 'IS NOT', 'id' => '!=')
            );
        }
    }

    /**
     * Get the parameter label. This is generally used implicitly in a magic method.
     * @return string The human-readable name of the parameter (for display purposes).
     */
    final public function getLabel()
    {
        return $this->label_;
    }

    /**
     * Retrieves a list of common items for the given search term. This is used for any parameter where the value_type is 'string_search'.
     * Override this function to specify how to retrieve the common items.
     * @param $term string Search term.
     * @return array The list of common items for the specified term.
     */
    public static function getCommonItemsForTerm($term)
    {
        // Override in subclasses where relevant
        return array($term);
    }

    /**
     * Override this function if the parameter subclass has extra validation rules.
     * If doing so, ensure you invoke the parent function first to obtain the initial list of rules and merge the arrays together.
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
     * @uses CFormModel::attributeNames()
     */
    public function getValueForAttribute($attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            return $this->$attribute;
        }
        throw new CException('Attribute does not exist.');
    }

    /**
     * Override this function to customise the output within the audit table. Generally it should be something like "name: < val".
     * @return string|null The audit string.
     */
    public function getAuditData()
    {
        return $this->name;
    }

    /**
     * Returns an array representing the parameter to store in the database. This array will be serialised before storage.
     * Subclasses should override this method and extend the array with any extra properties that need to be saved.
     * @return array
     */
    public function saveSearch()
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

    /**
     * Get the list of adder dialog options
     * @return string[] List of adder dialog options.
     */
    final public function getOptions()
    {
        return $this->options;
    }
}
