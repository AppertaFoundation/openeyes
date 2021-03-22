<?php

/**
 * This is the model class for table "patient_referral".
 *
 * The followings are the available columns in table 'patient_referral':
 * @property integer $patient_id
 * @property string $file_content
 * @property string $file_type
 * @property string $file_size
 * @property string $file_name
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Patient $patient
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class PatientReferral extends BaseActiveRecord
{
    public $uploadedFile;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_referral';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('uploadedFile', 'file', 'allowEmpty' => true),
            array('patient_id, uploadedFile, file_name, file_size, file_content, file_type', 'safe')
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
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'uploadedFile' => 'Referral'
        );
    }

    public function beforeValidate()
    {
        if (!$this->isNewRecord) {
            $this->setScenario('edit_patient');
        }
        return parent::beforeValidate();
    }

    /**
     * Populate the model with the main attributes from $FILE.
     * @return bool The beforeSave event.
     */
    protected function beforeSave()
    {
        if (
            !empty($_FILES['PatientReferral']['tmp_name']['uploadedFile'])
            && $_FILES['PatientReferral']['error']['uploadedFile'] === UPLOAD_ERR_OK
        ) {
            $tmp_name = $_FILES['PatientReferral']['tmp_name']['uploadedFile'];

            $this->file_size = $_FILES['PatientReferral']['size']['uploadedFile'];
            $this->file_name = $_FILES['PatientReferral']['name']['uploadedFile'];
            $this->file_content = file_get_contents($tmp_name);
            $this->file_type = $_FILES['PatientReferral']['type']['uploadedFile'];
        }

        return parent::beforeSave();
    }


}
