<?php
/**
 * This is the model class for table "ophdrpgdpsd_assignment_comment".
 *
 * The followings are the available columns in table 'ophdrpgdpsd_assignment_comment':
 * @property integer $id
 * @property string $comment
 * @property string $commented_by
 * @property integer $last_modified_user_id
 * @property string $last_modified_date
 * @property integer $created_user_id
 * @property string $created_date
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property User $commented_by
 */
class OphDrPGDPSD_Assignment_Comment extends \BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrpgdpsd_assignment_comment';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            // The following rule is used by search().
            array('comment, commented_by', 'safe'),
            array('id, text, commented_by', 'safe', 'on'=>'search'),
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'commented_user' => array(self::BELONGS_TO, 'User', 'commented_by'),
        );
    }

   /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'comment' => 'Comment',
            'commented_by' => 'Commented By',
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
        $criteria->compare('comment', $this->comment);
        $criteria->compare('commented_by', $this->commented_by);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphDrPGDPSD_Assignment_Comment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function __toString(){
        return $this->comment;
    }
}
