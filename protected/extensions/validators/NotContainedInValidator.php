<?php

/**
 * Created by PhpStorm.
 * User: petergallagher
 * Date: 27/03/15
 * Time: 16:00.
 */
class NotContainedInValidator extends CValidator
{
    /**
     * @var string Name of the model to be checked against
     */
    public $model;

    /**
     * @var string Foreign key if not standard tablename_id
     */
    public $foreignKey;

    /**
     * Validates a single attribute.
     *
     * @param CModel $object    the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     */
    protected function validateAttribute($object, $attribute)
    {
        if (!$object->isAttributeDirty($attribute)) {
            return;
        }

        if ($this->model === null) {
            //currently unsupported
            return;
        }

        if ($this->message === null) {
            $this->message = '{attribute} can not be edited because it is already in use';
        }

        if ($this->foreignKey === null) {
            $this->foreignKey = $object->tableName().'_id';
        }

        if ($this->validateContainedInModel($object->id)) {
            $this->addError($object, $attribute, $this->message);
        }
    }

    /**
     * Checks against the provided model to see if any foreign keys wit the current PK are found.
     *
     * @param $value
     *
     * @return CActiveRecord|null
     */
    protected function validateContainedInModel($value)
    {
        if (!class_exists($this->model) || !is_subclass_of($this->model, 'BaseActiveRecord')) {
            return;
        }
        $instance = new $this->model();

        return $instance::model()->findByAttributes(array("$this->foreignKey" => $value));
    }
}
