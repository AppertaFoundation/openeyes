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
     * @var string $operator .
     */
    public $operation;

    /**
     * @var integer $id .
     */
    public $id;

    /**
     * Get the parameter identifier (usually the name).
     * @return string The human-readable name of the parameter (for display purposes).
     */
    abstract public function getLabel();

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
            array('operation', 'required', 'message' => 'The search operator cannot be blank'),
            array('id', 'safe'),
        );
    }

    /**
     * Render the parameter on-screen.
     * @param $id integer The position of the parameter in the list of parameters.
     */
    abstract public function renderParameter($id);

    /**
     * Override this function to customise the output within the audit table. Generally it should be something like "name: < val".
     * @return string|null The audit string.
     */
    public function getAuditData()
    {
        return null;
    }

    protected function getDisplayTitle(){
        ob_start();
        ?>
        <div style="padding-right: 15px;">
            <p><?= $this->getLabel()?></p>
        </div>
        <?php
        return ob_get_clean();
    }
}