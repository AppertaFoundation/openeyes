<?php


class Element_OphCoDocument_Document extends BaseEventTypeElement
{


    /**
     * @return string
     */
    public function tableName()
    {
        return 'et_ophcodocument_document';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            //array('single_document_id', 'required'),
            array('event_id, single_document_id, left_document_id, right_document_id, event_sub_type', 'safe'),
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'single_document' => array(self::BELONGS_TO, 'ProtectedFile', 'single_document_id'),
            'left_document' => array(self::BELONGS_TO, 'ProtectedFile', 'left_document_id'),
            'right_document' => array(self::BELONGS_TO, 'ProtectedFile', 'right_document_id'),
            'sub_type' => array(self::BELONGS_TO, 'OphCoDocument_Sub_Types', 'event_sub_type'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'document_id' => 'Document'
        );
    }

    /**
     * @return string
     */
    public function logoPostfix() {
        if ($this->sub_type) {
            if ($this->sub_type->name == 'OCT') {
                return 'OCT';
            } else if ($this->sub_type->name == 'Photograph') {
                return 'Photograph';
            }
        }
        return '';
    }
}
