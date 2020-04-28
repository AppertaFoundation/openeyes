<?php

namespace OEModule\OphCiExamination\models;

class InvoiceStatus extends \BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_invoice_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
            array('id, name, active', 'safe', 'on' => 'search'),
            array('active', 'numerical', 'integerOnly'=>true),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
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
        $criteria->compare('active', $this->active, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

}
