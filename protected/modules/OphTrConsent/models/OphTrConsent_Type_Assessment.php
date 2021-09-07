<?php

/**
 * This is the model class for table "ophtrconsent_type_assessment".
 *
 * The followings are the available columns in table 'ophtrconsent_type_assessment':
 * @property integer $id
 * @property string $element_id
 * @property string $type_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property ElementType $element
 * @property OphtrconsentTypeType $type
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class OphTrConsent_Type_Assessment extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtrconsent_type_assessment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('element_id, type_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('display_order', 'length', 'max'=>4),
            array('last_modified_date, created_date', 'safe'),
            array('element_id', 'validateDuplicatedLayoutElement'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, element_id, type_id,display_order, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
        );
    }

    public function validateDuplicatedLayoutElement($attribute, $params)
    {
        if (
            $this::model()->count(
                'element_id=:element_id AND type_id=:type_id',
                array(':element_id' => $this->element_id,':type_id' => $this->type_id)
            ) > 0
        ) {
            $this->addError($attribute, "This element has been added to this layout!");
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element' => array(self::BELONGS_TO, 'ElementType', 'element_id'),
            'type' => array(self::BELONGS_TO, 'OphtrconsentTypeType', 'type_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
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
            'type_id' => 'Type',
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
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('type_id', $this->type_id, true);
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
     * @return OphtrconsentTypeAssessment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * A consent form is signed if at least one of the signatures
     * (consultant or secretary) is done
     *
     * @return bool
     */
    public function existsElementInConsentForm($element_id, $type_id) : bool
    {
        return !empty(self::model()->findByAttributes(array('element_id' => $element_id, 'type_id' => $type_id)));
    }
}
