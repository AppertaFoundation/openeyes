<?php
namespace OEModule\OphCiExamination\models;
/**
 * This is the model class for table "et_ophciexamination_safeguarding".
 *
 * The followings are the available columns in table 'et_ophciexamination_safeguarding':
 * @property integer $id
 * @property integer $event_id
 * @property integer $no_concerns
 * @property integer $outcome_id
 * @property string $outcome_comments
 * @property bool $has_social_worker
 * @property bool $under_protection_plan
 * @property string $accompanying_person_name
 * @property string $responsible_parent_name
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property OphciexaminationSafeguardingEntry[] $entries
 */
class Element_OphCiExamination_Safeguarding extends \BaseEventTypeElement
{
    public const NO_SAFEGUARDING_CONCERNS = 1;
    public const CONFIRM_SAFEGUARDING_CONCERNS = 2;
    public const FOLLOWUP_REQUIRED = 3;

    use traits\CustomOrdering;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_safeguarding';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, no_concerns', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('no_concerns, outcome_id, has_social_worker, under_protection_plan, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, no_concerns, outcome_id, has_social_worker, under_protection_plan, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'entries' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Entry', 'element_id'),
            'outcome' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Outcome', 'outcome_id'),
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
            'no_concerns' => 'No Concerns',
            'outcome_id' => 'Outcome',
            'outcome_comments' => 'Outcome Comments',
            'has_social_worker' => 'Has Social Worker',
            'under_protection_plan' => 'Under Protection Plan',
            'accompanying_person_name' => 'Accompanying Person',
            'responsible_parent_name' => 'Guardian',
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
        $criteria->compare('no_concerns', $this->no_concerns);
        $criteria->compare('outcome_id', $this->outcome_id);
        $criteria->compare('outcome_comments', $this->outcome_comments);
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
     * @return Element_OphCiExamination_Safeguarding the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function afterValidate()
    {
        if (!$this->no_concerns && !$this->entries && !isset($_POST[\CHtml::modelName($this)]['entries'])) {
            $this->addError('no_concerns', 'Please confirm patient has no safeguarding concerns');
        }

        parent::afterValidate();
    }
}
