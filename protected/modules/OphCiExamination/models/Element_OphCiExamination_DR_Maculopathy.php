<?php

namespace OEModule\OphCiExamination\models;

use CActiveDataProvider;
use CDbCriteria;
use SplitEventTypeElement;

/**
 * This is the model class for table "et_ophciexamination_dr_maculopathy".
 *
 * The followings are the available columns in table 'et_ophciexamination_dr_maculopathy':
 * @property integer $id
 * @property integer $event_id
 * @property string $overall_grade
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 */

class Element_OphCiExamination_DR_Maculopathy extends SplitEventTypeElement
{
    use traits\CustomOrdering;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_dr_maculopathy';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id', 'numerical', 'integerOnly'=>true),
            array('left_overall_grade', 'length', 'max'=>255),
            array('right_overall_grade', 'length', 'max'=>255),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date, eye_id', 'safe'),
            array('left_maculopathy_features', 'requiredIfSide', 'side' => 'left'),
            array('right_maculopathy_features', 'requiredIfSide', 'side' => 'right'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, overall_grade, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'features' => array(
                self::MANY_MANY,
                'OphCiExamination_DRGrading_Feature',
                'ophciexamination_dr_maculopathy_entry(element_id, feature_id)',
            ),
            'left_maculopathy_features' => array(
                self::HAS_MANY,
                MaculopathyFeature::class,
                'element_id',
                'on' => ('left_maculopathy_features.eye_id = '.\Eye::LEFT),
            ),
            'right_maculopathy_features' => array(
                self::HAS_MANY,
                MaculopathyFeature::class,
                'element_id',
                'on' => ('right_maculopathy_features.eye_id = '.\Eye::RIGHT),
            ),
            'maculopathy_features' => array(
                self::HAS_MANY,
                MaculopathyFeature::class,
                'element_id',
            ),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
        );
    }

    public function sidedFields()
    {
        return array('overall_grade');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'overall_grade' => 'Overall Grade',
            'left_maculopathy_features' => 'Maculopathy Features',
            'right_maculopathy_features' => 'Maculopathy Features',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id);
        $criteria->compare('overall_grade', $this->overall_grade, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphCiExamination_DR_Maculopathy the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $side int ID of the eye relating to the element. Handled values are either Eye::LEFT or Eye::RIGHT.
     * @param $features array List of maculopathy feature entries to be added. This is an array of arrays of field values.
     * @throws \Exception Unable to save or delete maculopathy features.
     */
    public function updateFeatures($side, $features)
    {
        $curr_by_id = array();
        $save = array();

        foreach ($this->maculopathy_features as $r) {
            if ($r->eye_id == $side) {
                $curr_by_id[$r->feature->id] = $r;
            }
        }

        foreach ($features as $feature) {
            if (!array_key_exists($feature['feature_id'], $curr_by_id)) {
                $obj = new MaculopathyFeature();
            } else {
                $obj = $curr_by_id[$feature['feature_id']];
                unset($curr_by_id[$feature['feature_id']]);
            }
            $obj->attributes = $feature;
            $obj->element_id = $this->id;
            $obj->eye_id = $side;
            $save[] = $obj;
        }

        foreach ($save as $s) {
            if (!$s->save()) {
                throw new \Exception('unable to save feature:'.print_r($s->getErrors(), true));
            }
        }

        foreach ($curr_by_id as $curr) {
            if (!$curr->delete()) {
                throw new \Exception('unable to delete feature:'.print_r($curr->getErrors(), true));
            }
        }
    }
}
