<?php

/**
 * Created by PhpStorm.
 * User: PATELH3
 * Date: 26/11/2015
 * Time: 14:03.
 */
class DicomFileLog extends BaseActiveRecordVersioned
{
    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Benefit the static model class
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
        return 'dicom_file_log';
    }

    public function relations()
    {
        return array(
            'dicom_file_id' => array(self::HAS_MANY, 'DicomFiles', 'id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('filename', 'required'),
            array(
                'event_date_time, filename, status, process_name',
                'safe',
            ),
        );
    }
}
