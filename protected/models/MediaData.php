<?php

class MediaData extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return ProtectedFile the static model class
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
        return 'media_data';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('media_name, original_file_path, original_file_name, original_file_size, original_file_date, media_type_id', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'mediatype' => array(self::BELONGS_TO, 'MediaType', 'media_type_id'),
        );
    }

    /**
     * Path to protected file storage.
     *
     * @return string
     */
    public static function getBasePath()
    {
        return Yii::app()->basePath.'/media_data';
    }

    public function getPath()
    {
        return $this->getBasePath().'/'.$this->id;
    }
}
