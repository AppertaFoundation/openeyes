<?php

/**
 * This is the model class for table "signature_import_log".
 *
 * The followings are the available columns in table 'signature_import_log':
 * @property integer $id
 * @property string $filename
 * @property string $return_message
 * @property datetime $import_datetime
 * @property integer $status_id

 */
class SignatureImportLog extends BaseActiveRecord
{

    const STATUS_FAILED = 3;
    const STATUS_SUCCESS = 4;
    const STATUS_MANUAL = 5;
    const STATUS_MANUAL_IGNORE = 6;
    const TYPE_CVI = 1;
    const TYPE_CONSENT = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'signature_import_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('filename, status_id', 'required'),
            array('status_id', 'numerical', 'integerOnly'=>true),
            array('filename, return_message, import_datetime', 'safe'),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SignatureImportLog the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getStatusName()
    {
        switch ($this->status_id) {
            case self::STATUS_FAILED:
                $status_name = 'Failed';
                break;
            case self::STATUS_SUCCESS:
                $status_name = 'Success';
                break;
            case self::STATUS_MANUAL:
                $status_name = 'Manual';
                break;
            case self::STATUS_MANUAL_IGNORE:
                $status_name = 'Manual Ignore';
                break;
            default:
                $status_name = "N/A";
                break;
        }
        return $status_name;
    }

    public function getStatusColor()
    {
        switch ($this->status_id) {
            case self::STATUS_FAILED:
                $status_color = '#C0172F';
                break;
            case self::STATUS_SUCCESS:
                $status_color = '#98BF64';
                break;
            case self::STATUS_MANUAL:
                $status_color = '#FDA50F';
                break;
            case self::STATUS_MANUAL_IGNORE:
                $status_color = '#FA8072';
                break;
            default:
                $status_color = "#CCCCCC";
                break;
        }
        return $status_color;
    }
}
