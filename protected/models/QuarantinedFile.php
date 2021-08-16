<?php

/**
 * This is the model class for table "quarantined_file".
 *
 * The followings are the available columns in table 'quarantined_file':
 * @property integer $id
 * @property string $original_uid
 * @property string $quarantine_reason
 */
class QuarantinedFile extends CActiveRecord
{
    private const PROTECTED_DIR = '/var/www/openeyes/protected/files/';
    private const QUARANTINED_DIR = '/var/www/openeyes/protected/files/quarantine/';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'quarantined_file';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('original_uid, quarantine_reason', 'required'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, original_uid, quarantine_reason', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'original_uid' => 'Original UID',
            'quarantine_reason' => 'Quarantine Reason',
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
        $criteria->compare('original_uid', $this->original_uid, true);
        $criteria->compare('quarantine_reason', $this->quarantine_reason, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return QuarantinedFile the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function createFromProtectedFile($original_uid, $reason)
    {
        $quarantined_file = new QuarantinedFile();

        $quarantined_file->original_uid = $original_uid;
        $quarantined_file->quarantine_reason = $reason;

        //Inherit all applicable attributes from protected file
        //Copy existing protected file to quarantined folder, and strip permissions from it (Especially execute!)

        return $quarantined_file;
    }

    /**
     * Full file path prepended with quarantined directory.
     *
     * @return string
     */
    public function getQuarantinedUID() {
        $trimmed_uid = str_replace(self::PROTECTED_DIR, '', $this->original_uid);

        return self::QUARANTINED_DIR . $trimmed_uid;
    }

    /**
     * Full file path prepended with quarantined directory.
     *
     * @return string
     */
    public function getOriginalUID() {
        return $this->original_uid;
    }
}
