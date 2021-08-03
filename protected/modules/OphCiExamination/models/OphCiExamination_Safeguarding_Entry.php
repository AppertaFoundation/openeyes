<?php
namespace OEModule\OphCiExamination\models;
/**
 * This is the model class for table "ophciexamination_safeguarding_entry".
 *
 * The followings are the available columns in table 'ophciexamination_safeguarding_entry':
 * @property integer $id
 * @property integer $element_id
 * @property integer $concern_id
 * @property string $comment
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property OphCiExamination_Safeguarding_Concern $concern
 * @property User $createdUser
 * @property Element_OphCiExamination_Safeguarding $element
 * @property User $lastModifiedUser
 * @property OphCiExamination_Safeguarding_Outcome $outcome
 */
class OphCiExamination_Safeguarding_Entry extends \BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_safeguarding_entry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, concern_id', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('comment, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_id, concern_id, comment, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'concern' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Concern', 'concern_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'element' => array(self::BELONGS_TO, 'EtOphciexaminationSafeguarding', 'element_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'element_id' => 'Element',
            'concern_id' => 'Concern',
            'comment' => 'Comment',
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
        $criteria->compare('element_id', $this->element_id);
        $criteria->compare('concern_id', $this->concern_id);
        $criteria->compare('comment', $this->comment, true);
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
     * @return OphCiExamination_Safeguarding_Entry the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}