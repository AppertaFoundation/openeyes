<?php

use OE\factories\models\traits\HasFactory;

/**
 * Created by PhpStorm.
 * User: PATELH3
 * Date: 11/12/2015
 * Time: 14:51.
 */
class Eye_Status extends BaseActiveRecord
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Eye the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'dicom_eye_status';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id,name', 'safe'),
            //array('patient_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id', 'safe', 'on' => 'search'),
        );
    }

    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}
