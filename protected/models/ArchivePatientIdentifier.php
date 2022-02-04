<?php

/**
 * This is the model class for table "patient_identifier".
 *
 * The followings are the available columns in table 'patient_identifier':
 * @property integer $id
 * @property string $patient_id
 * @property string code
 * @property string $value
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class ArchivePatientIdentifier extends BaseActiveRecordVersioned
{
    private $_config;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'archive_patient_identifier';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('patient_id, code', 'required'),
            array('patient_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('code', 'length', 'max' => 50),
            array('value', 'length', 'max' => 255),
            array('value','numerical'),
            array('last_modified_date, created_date', 'safe'),
        );

        $config = $this->getConfig();

        if ($config && isset($config['required']) && $config['required'] === true) {
            $rules[] = array('value', 'required');
        }

        if ($config && isset($config['validate_pattern'])) {
            $rule = array('value', 'match', 'pattern' => $config['validate_pattern']);
            if (isset($config['validate_msg'])) {
                $rule['message'] = $config['validate_msg'];
            }
            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'code' => 'Code',
            'value' => 'Value',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    private function getConfigOption($option)
    {
        return isset($this->getConfig()[$option]) ? $this->getConfig()[$option] : null;
    }

    public function hasAutoIncrement()
    {
        return $this->getConfigOption('auto_increment') === true;
    }

    public function getStartVal()
    {
        return $this->getConfigOption('start_val');
    }

    public function isRequired()
    {
        return $this->getConfigOption('required') === true;
    }
//Currently used for RVEEh UR number  - to check if validation is to be performed on null or empty values from patient identifier in common.php
    public function nullCheck()
    {
        return $this->getConfigOption('allow_null_check');
    }

    public function isEditable()
    {
        return $this->getConfigOption('editable') === true || $this->getConfigOption('editable') === null;
    }

    public function mustBeUnique()
    {
        return $this->getConfigOption('unique') === true;
    }

    public function getConflictMessage()
    {
        return $this->getConfigOption('conflict_msg') ?: $this->getLabel() . ' must be unique';
    }

    public function hasValue()
    {
        return isset($this->value) && trim($this->value) !== '';
    }

    public function displayIfEmpty()
    {
        return $this->getConfigOption('display_if_empty') === true;
    }

    public function getConfig()
    {
        if ($this->_config === null) {
            $this->_config = isset(Yii::app()->params['patient_identifiers'][$this->code]) ? Yii::app()->params['patient_identifiers'][$this->code] : null;
        }

        return $this->_config;
    }

    public function getLabel()
    {
        return $this->getConfigOption('label') ?: ucwords(strtolower(str_replace('_', ' ', $this->code)));
    }

    public function getPlaceholder()
    {
        return $this->getConfigOption('placeholder') ?: $this->getLabel();
    }

    protected function beforeValidate()
    {
        if (!$this->hasValue() && $this->hasAutoIncrement()) {
            $last_identifier = self::model()->find(array(
                    'condition' => 'code = :code',
                    'order' => 'CONVERT(value, INTEGER) DESC',
                    'params' => array(':code' => $this->code),
                )
            );

            if ($last_identifier) {
                $this->value = $last_identifier->value + 1;
            } elseif ($this->getStartVal() !== null) {
                $this->value = $this->getStartVal();
            } elseif ($this->isRequired() && $this->isEditable()) {
                $this->value = 1;
            }
        }

        if ($this->hasValue()
            && $this->mustBeUnique()
            && self::model()->exists('code = :code AND value = :value AND id != :id',
                array(':code' => $this->code, ':value' => $this->value, ':id' => $this->id ?: -1)
            )) {
            $this->addError('value', $this->getConflictMessage());
        }

        return parent::beforeValidate();
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ArchivePatientIdentifier the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
