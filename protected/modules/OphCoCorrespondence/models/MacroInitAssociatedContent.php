<?php

/**
 * This is the model class for table "macro_init_associated_content".
 *
 * The followings are the available columns in table 'macro_init_associated_content':
 * @property string $id
 * @property string $macro_id
 * @property integer $is_system_hidden
 * @property integer $is_print_appended
 * @property string $init_method_id
 * @property string $init_method
 * @property string $init_protected_file_id
 * @property string $short_code
 * @property integer $display_order
 * @property string $display_title
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property OphcorrespondenceInitMethod $initMethod
 * @property OphcocorrespondenceLetterMacro $macro
 * @property ProtectedFile $initProtectedFile
 */
class MacroInitAssociatedContent extends BaseActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'macro_init_associated_content';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('macro_id, is_system_hidden, is_print_appended, init_method_id, short_code', 'required'),
            array('is_system_hidden, is_print_appended, display_order', 'numerical', 'integerOnly'=>true),
            array('macro_id, init_method_id, init_protected_file_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('short_code, display_title', 'length', 'max'=>45),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, macro_id, is_system_hidden, is_print_appended, init_method_id, init_protected_file_id, short_code, display_order, display_title, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'initMethod' => array(self::BELONGS_TO, 'OphcorrespondenceInitMethod', 'init_method_id'),
            'macro' => array(self::BELONGS_TO, 'OphcocorrespondenceLetterMacro', 'macro_id'),
            'initProtectedFile' => array(self::BELONGS_TO, 'ProtectedFile', 'init_protected_file_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'macro_id' => 'Macro',
            'is_system_hidden' => 'Is System Hidden',
            'is_print_appended' => 'Is Print Appended',
            'init_method_id' => 'Init Method',
            'init_protected_file_id' => 'Init Protected File',
            'short_code' => 'Short Code',
            'display_order' => 'Display Order',
            'display_title' => 'Display Title',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('macro_id',$this->macro_id,true);
        $criteria->compare('is_system_hidden',$this->is_system_hidden);
        $criteria->compare('is_print_appended',$this->is_print_appended);
        $criteria->compare('init_method_id',$this->init_method_id,true);
        $criteria->compare('init_protected_file_id',$this->init_protected_file_id,true);
        $criteria->compare('short_code',$this->short_code,true);
        $criteria->compare('display_order',$this->display_order);
        $criteria->compare('display_title',$this->display_title,true);
        $criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
        $criteria->compare('last_modified_date',$this->last_modified_date,true);
        $criteria->compare('created_user_id',$this->created_user_id,true);
        $criteria->compare('created_date',$this->created_date,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MacroInitAssociatedContent the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
