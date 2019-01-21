<?php

/**
 * Class PatientDeceasedParameter
 */
class PatientDeceasedParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_deceased';
        $this->operation = false;
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Patient Deceased';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return parent::attributeNames();
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('operation', 'boolean'),
        ));
    }

    public function renderParameter($id)
    {
        // Initialise any rendering variables here.
        ?>
      <!-- Place screen-rendering code here. -->

        <div class="flex-layout flex-left">
            <?= $this->getDisplayTitle() ?>
            <div>
                <?php echo CHtml::activeCheckBox($this, "[$id]operation"); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        switch ($this->operation)
        {
            case '0':
                return 'SELECT id FROM patient WHERE NOT(is_deceased)';
                break;
            case '1':
                return 'SELECT id FROM patient';
                break;
            default:
                throw new CHttpException(400, "Invalid value specified: $this->operation");
                break;
        }
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        // No binds are used in this query, so return an empty array.
        return array();
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        $value = $this->operation === false ? 'False' : 'True';
        return "$this->name: $value";
    }
}
