<?php

namespace OEModule\OphDrPGDPSD\models;

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "et_drug_administration_assignments".
 *
 * The followings are the available columns in table 'et_drug_administration_assignments':
 * @property integer $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 * @property Event $event
 */

class Element_DrugAdministration_Assignments extends \BaseActiveRecordVersioned
{
    use HasFactory;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_drug_administration_assignments';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, assignment_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date, element_id, assignment_id', 'safe'),
            // The following rule is used by search().
            array('id, element_id, assignment_id', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array_merge(
            parent::relations(),
            array(
                'element' => array(self::BELONGS_TO, Element_DrugAdministration::class, 'element_id'),
                'assignment' => array(self::BELONGS_TO, OphTrPGDPSD_assignment::class, 'assignment_id'),
            )
        );
    }
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('type', $this->type, true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_DrugAdministration the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
