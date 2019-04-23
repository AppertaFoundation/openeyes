<?php


class OphInLabResults_Type extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphInLabResults_Type static model class
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
        return 'ophinlabresults_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, result_element_id, ', 'required'),
            array('type, result_element_id, field_type_id, default_units, custom_warning_message, min_range, max_range,
            normal_min, normal_max, show_on_whiteboard ', 'safe'),
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
            'result_element_type' => array(self::BELONGS_TO, 'ElementType', 'result_element_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'fieldType' => [self::BELONGS_TO, 'OphInLabResults_Field_Type', 'field_type_id']
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'type' => 'Type',
            'result_element_type' => 'Result Type',
        );
    }
}
