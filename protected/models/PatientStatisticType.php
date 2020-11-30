<?php

/**
 * This is the model class for table "patient_statistic_type".
 *
 * The followings are the available columns in table 'patient_statistic_type':
 * @property string $mnem
 * @property string $title
 * @property string $x_axis_label
 * @property string $y_axis_label
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property PatientStatistic[] $patient_statistics
 * @property User $created_user
 * @property User $last_modified_user
 */
class PatientStatisticType extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_statistic_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('mnem, title', 'required'),
            array('mnem, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('title, x_axis_label, y_axis_label', 'length', 'max' => 255),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array(
                'mnem, title, x_axis_label, y_axis_label, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on' => 'search'
            ),
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
            'patient_statistics' => array(self::HAS_MANY, 'PatientStatistic', 'stat_type_mnem'),
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'mnem' => 'Mnem',
            'title' => 'Title',
            'x_axis_label' => 'X Axis Label',
            'y_axis_label' => 'Y Axis Label',
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
        $criteria = new CDbCriteria();

        $criteria->compare('mnem', $this->mnem, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('x_axis_label', $this->x_axis_label, true);
        $criteria->compare('y_axis_label', $this->y_axis_label, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array('criteria' => $criteria));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BaseActiveRecord
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
