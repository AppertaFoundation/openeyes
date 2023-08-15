<?php

/**
 * Created by PhpStorm.
 * User: mike
 * Date: 10/11/15
 * Time: 16:57.
 */

namespace OEModule\OphCiExamination\models;

class Element_OphCiExamination_Fundus extends \SplitEventTypeElement
{
    use traits\CustomOrdering;

    public $exclude_element_from_empty_discard_check = true;

    public $auto_update_relations = true;
    public $relation_defaults = array(
        'left_vitreous' => array(
            'side_id' => \Eye::LEFT,
        ),
        'right_vitreous' => array(
            'side_id' => \Eye::RIGHT
        ));

    public $service;

    // used for the letter string method in the eyedraw element behavior
    public $letter_string_prefix = "Fundus:\n";

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors()
    {
        return array(
            'EyedrawElementBehavior' => array(
                'class' => 'application.behaviors.EyedrawElementBehavior',
            )
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_fundus';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('eye_id, left_eyedraw, left_ed_report, left_description, right_eyedraw, right_ed_report, right_description, left_vitreous, right_vitreous', 'safe'),
            array('left_eyedraw', 'requiredIfSide', 'side' => 'left'),
            array('right_eyedraw', 'requiredIfSide', 'side' => 'right'),
            array('left_ed_report', 'requiredIfNoComments', 'side' => 'left', 'comments_attribute' => 'description'),
            array('right_ed_report', 'requiredIfNoComments', 'side' => 'right', 'comments_attribute' => 'description'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, left_eyedraw, right_eyedraw, left_description, right_description, eye_id', 'safe', 'on' => 'search'),
        );
    }

    public function sidedFields()
    {
        return array('description', 'eyedraw', 'description');
    }

    public function canCopy()
    {
        return true;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'left_vitreous' => array(self::MANY_MANY, 'OEModule\OphCiExamination\models\Vitreous',
                                'ophciexamination_fundus_vitreous(element_id, vitreous_id)',
                                'on' => 'side_id = ' . \Eye::LEFT,
                                'order' => 'left_vitreous.display_order asc'),
            'right_vitreous' => array(self::MANY_MANY, 'OEModule\OphCiExamination\models\Vitreous',
                                'ophciexamination_fundus_vitreous(element_id, vitreous_id)',
                                'on' => 'side_id = ' . \Eye::RIGHT,
                                'order' => 'right_vitreous.display_order asc')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'left_eyedraw' => 'Eyedraw',
            'left_ed_report' => 'Report',
            'left_description' => 'Comments',
            'right_eyedraw' => 'Eyedraw',
            'right_ed_report' => 'Report',
            'right_description' => 'Comments',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('left_eyedraw', $this->left_eyedraw);
        $criteria->compare('left_description', $this->left_description);
        $criteria->compare('right_eyedraw', $this->right_eyedraw);
        $criteria->compare('right_description', $this->right_description);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}
