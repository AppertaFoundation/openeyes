<?php

/**
 * This is the model class for table "event_associated_content".
 *
 * The followings are the available columns in table 'event_associated_content':
 * @property string $id
 * @property string $parent_event_id
 * @property integer $is_system_hidden
 * @property integer $is_print_appended
 * @property string $short_code
 * @property string $association_storage
 * @property string $associated_event_id
 * @property string $associated_protected_file_id
 * @property string $associated_url
 * @property integer $display_order
 * @property string $display_title
 *
 * The followings are the available model relations:
 * @property Event $parentEvent
 * @property Event $associatedEvent
 * @property ProtectedFile $associatedProtectedFile
 */
class EventAssociatedContent extends BaseActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_associated_content';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('parent_event_id, is_system_hidden, is_print_appended, short_code, association_storage, display_order', 'required'),
            array('init_associated_content_id , is_system_hidden, is_print_appended, display_order', 'numerical','integerOnly'=>true),
            array('parent_event_id, association_storage, associated_event_id, associated_protected_file_id', 'length', 'max'=>10),
            array('short_code', 'length', 'max'=>45),
            array('associated_url', 'length', 'max'=>255),
            array('display_title', 'length', 'max'=>80),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, parent_event_id,init_associated_content_id, is_system_hidden, is_print_appended, short_code, association_storage, associated_event_id, associated_protected_file_id, associated_url, display_order, display_title', 'safe', 'on'=>'search'),
            array('id, parent_event_id,init_associated_content_id, is_system_hidden, is_print_appended, short_code, association_storage, associated_event_id, associated_protected_file_id, associated_url, display_order, display_title', 'safe'),
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
            'parentEvent' => array(self::BELONGS_TO, 'Event', 'parent_event_id'),
            'initAssociatedContent' => array(self::BELONGS_TO, 'MacroInitAssociatedContent', 'init_associated_content_id'),
            'associatedEvent' => array(self::BELONGS_TO, 'Event', 'associated_event_id'),
            'associatedProtectedFile' => array(self::BELONGS_TO, 'ProtectedFile', 'associated_protected_file_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'parent_event_id' => 'Parent Event',
            'is_system_hidden' => 'Is System Hidden',
            'is_print_appended' => 'Is Print Appended',
            'short_code' => 'Short Code',
            'association_storage' => 'Association Storage',
            'associated_event_id' => 'Associated Event',
            'associated_protected_file_id' => 'Associated Protected File',
            'associated_url' => 'Associated Url',
            'display_order' => 'Display Order',
            'display_title' => 'Display Title',
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('parent_event_id', $this->parent_event_id, true);
        $criteria->compare('init_associated_content_id', $this->init_associated_content_id);
        $criteria->compare('is_system_hidden', $this->is_system_hidden);
        $criteria->compare('is_print_appended', $this->is_print_appended);
        $criteria->compare('short_code', $this->short_code, true);
        $criteria->compare('association_storage', $this->association_storage, true);
        $criteria->compare('associated_event_id', $this->associated_event_id, true);
        $criteria->compare('associated_protected_file_id', $this->associated_protected_file_id, true);
        $criteria->compare('associated_url', $this->associated_url, true);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('display_title', $this->display_title, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


}
