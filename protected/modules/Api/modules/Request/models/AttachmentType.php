<?php

/**
 * This is the model class for table "attachment_type".
 *
 * The followings are the available columns in table 'attachment_type':
 * @property string $attachment_type
 * @property string $title_full
 * @property string $title_short
 * @property string $title_abbreviated
 *
 * The followings are the available model relations:
 * @property AttachmentData[] $attachmentDatas
 */
class AttachmentType extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AttachmentType the static model class
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
        return 'attachment_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['attachment_type', 'required'],
            ['attachment_type, title_full, title_short, title_abbreviated', 'length', 'max' => 45],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['attachment_type, title_full, title_short, title_abbreviated', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'attachmentDatas' => [self::HAS_MANY, 'AttachmentData', 'attachment_type'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'attachment_type' => 'Attachment Type',
            'title_full' => 'Title Full',
            'title_short' => 'Title Short',
            'title_abbreviated' => 'Title Abbreviated',
        ];
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

        $criteria = new CDbCriteria;

        $criteria->compare('attachment_type', $this->attachment_type, true);
        $criteria->compare('title_full', $this->title_full, true);
        $criteria->compare('title_short', $this->title_short, true);
        $criteria->compare('title_abbreviated', $this->title_abbreviated, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }
}
