<?php
namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_clinicoutcome_risk_status".
 *
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property string $description
 */
class OphCiExamination_ClinicOutcome_Risk_Status extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_ClinicOutcome_Risk_Status the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_clinicoutcome_risk_status';
    }

    /**
     * @return array validation rules for model
     */
    public function rules()
    {
        return array(
            array('name', 'length', 'max' => 10),
            array('alias', 'length', 'max' => 20),
            array('description', 'length', 'max' => 255),
            array('name, alias, description', 'required'),
            array('name, alias, description', 'unique', 'message' => 'Duplicate {attribute} entered.'),
            array('name, alias, description', 'safe'),
            array('id, name, alias, description', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => 'Name',
            'alias' => 'Alias',
            'description' => 'Description',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('alias', $this->alias, true);
        $criteria->compare('description', $this->description, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getIndicatorColor()
    {
        switch (strtolower($this->name)) {
            case 'low':
                return 'green';
            case 'medium':
                return 'amber';
            case 'high':
                return 'red';
        }
    }
}
