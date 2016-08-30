<?php


namespace OEModule\OphCoCvi\models;

/**
 * Class Element_OphCoCvi_Demographics
 *
 * @package OEModule\OphCoCvi\models
 *
 * @property string name
 */
class Element_OphCoCvi_Demographics extends \BaseEventTypeElement
{
    /**
     * @param null|string $className
     *
     * @return Element_OphCoCvi_Demographics|mixed
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
        return 'et_ophcocvi_demographics';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, name, date_of_birth, address, email, telephone, gender, gp_name, gp_address, gp_telephone', 'safe'),
            array('name, date_of_birth, address, telephone, gender, gp_name, gp_address, gp_telephone', 'required'),
            array('date_of_birth', 'OEDateValidatorNotFuture'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element_type' => array(
                self::HAS_ONE,
                'ElementType',
                'id',
                'on' => "element_type.class_name='" . get_class($this) . "'"
            ),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => 'Name',
            'date_of_birth' => 'Date of Birth',
            'nhs_number' => 'NHS Number',
            'address' => 'Address',
            'email' => 'Email',
            'telephone' => 'Telephone',
            'gender' => 'Gender',
            'gp_name' => 'GP\'s Name',
            'gp_address' => 'GP\'s Address',
            'gp_telephone' => 'GP\'s Telephone',
        );
    }

    /**
     * Initialises the element from the patient model.
     *
     * @param \Patient $patient
     *
     * @throws \Exception
     */
    public function initFromPatient(\Patient $patient)
    {
        $this->name = $patient->getFullName();
        $this->date_of_birth = $patient->dob;
        $this->nhs_number = $patient->getNhsnum();
        $this->address = $patient->getSummaryAddress(',');
        $this->email = $patient->getEmail();
        $this->telephone = $patient->getPrimary_phone();
        $this->gender = $patient->getGenderString();

        if($patient->gp){
            $this->gp_name = $patient->gp->getFullName();
            $this->gp_address = $patient->gp->getLetterAddress(array('delimiter' => ',', 'patient' => $patient));
            $this->gp_telephone = $patient->practice->phone;
        }
    }

    /**
     * Return the element data 
     * @return array
     */
    public function getStructuredDataForPrint()
    {
        return array(
            'patientName' => $this->name,
            'otherNames' => '',
            'patientDateOfBirth' => $this->date_of_birth,
            'nhsNumber' => $this->nhs_number,
            'gender' => $this->gender,
            'patientAddress' => $this->address,
            'patientEmail' => $this->email,
            'patientTel' => $this->telephone,
            'gpName' => $this->gp_name,
            'gpAddress' => $this->gp_address,
            'gpTel' => $this->gp_telephone,
        );
    }
}