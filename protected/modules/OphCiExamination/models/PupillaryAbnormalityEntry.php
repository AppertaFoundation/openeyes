<?php


namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_pupillary_abnormality_entry".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $element_id
 * @property int $abnormality_id
 * @property int $has_abnormality
 * @property string $comments
 * @property int $eye_id
 */
class PupillaryAbnormalityEntry extends \BaseElement
{
    public static $PRESENT = 1;
    public static $NOT_PRESENT = 0;
    public static $NOT_CHECKED = -9;
    const LEFT = 1;
    const RIGHT = 2;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return PreviousOperation the static model class
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
        return 'ophciexamination_pupillary_abnormality_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, abnormality_id, comments, has_abnormality, eye_id', 'safe'),
            array('abnormality_id', 'required'),
            array('has_abnormality', 'required', 'message'=>'Status cannot be blank'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, element_id, abnormality_id, comments, has_abnormality, eye_id', 'safe', 'on' => 'search'),
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
            'element' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\PupillaryAbnormalities', 'element_id'),
            'abnormality' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_PupillaryAbnormalities_Abnormality', 'abnormality_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
        );
    }

    protected function beforeSave()
    {
        if ($this->isModelDirty()) {
            $this->element->addAudit('edited-abnormalities');
        }
        return parent::beforeSave();
    }

    /**
     * @return string
     */
    public function getDisplayAbnormality()
    {
        return $this->abnormality ? $this->abnormality->name : '';
    }
}