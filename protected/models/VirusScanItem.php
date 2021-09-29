<?php

/**
 * This is the model class for table "virus_scan_item".
 *
 * The followings are the available columns in table 'virus_scan_item':
 * @property integer $id
 * @property integer $parent_scan_id
 * @property string $file_uid
 * @property string $scan_result
 * @property string $details
 *
 * The followings are the available model relations:
 * @property VirusScan $parentScan
 */
class VirusScanItem extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'virus_scan_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('parent_scan_id, file_uid, scan_result', 'required'),
            array('parent_scan_id', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, parent_scan_id, file_uid, scan_result, details', 'safe', 'on'=>'search'),
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
            'parentScan' => array(self::BELONGS_TO, 'VirusScan', 'parent_scan_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'parent_scan_id' => 'Parent Scan ID',
            'file_uid' => 'File UID',
            'scan_result' => 'Scan Result',
            'details' => 'Details',
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
        $criteria->compare('parent_scan_id', $this->parent_scan);
        $criteria->compare('file_uid', $this->file_uid, true);
        $criteria->compare('scan_result', $this->scan_result, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return VirusScanItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function newFromScanResult($parent_id, $scan_result)
    {
        $new_result_item = new VirusScanItem();

        $new_result_item->parent_scan_id = $parent_id;
        $new_result_item->file_uid = $scan_result['uid'];
        $new_result_item->scan_result = $scan_result['status'];
        $new_result_item->details = $scan_result['details'];

        return $new_result_item;
    }

    public function getFileName()
    {
        return ProtectedFile::model()->findByAttributes(array('uid' => $this->file_uid))->name;
    }
}
