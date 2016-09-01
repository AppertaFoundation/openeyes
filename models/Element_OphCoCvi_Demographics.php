<?php


namespace OEModule\OphCoCvi\models;

/**
 * Class Element_OphCoCvi_Demographics
 *
 * @package OEModule\OphCoCvi\models
 *
 * @property int $event_id
 * @property string $title_surname
 * @property string $other_names
 * @property date $date_of_birth
 * @property string $address
 * @property string $postcode
 * @property string $email
 * @property string $telephone
 * @property int $gender_id
 * @property int $ethnic_group_id
 * @property string $nhs_number
 * @property string $gp_name
 * @property string $gp_address
 * @property string $gp_telephone
 *
 * @property \EthnicGroup $ethnic_group
 * @property \Gender $gender
 * @property \Event $event
 * @property \User $usermodified
 * @property \User $user
 * @property \EventType $eventType
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
            array(
                'event_id, title_surname, other_names, date_of_birth, address, postcode, email, telephone, gender_id, ethnic_group_id, nhs_number, gp_name, gp_address, gp_telephone',
                'safe'
            ),
            array(
                'title_surname, other_names, date_of_birth, address, postcode, telephone, gender_id, ethnic_group_id, nhs_number, gp_name, gp_address, gp_telephone',
                'required',
                'on' => 'finalise'
            ),
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
            'gender' => array(self::BELONGS_TO, 'Gender', 'gender_id'),
            'ethnic_group' => array(self::BELONGS_TO, 'EthnicGroup', 'ethnic_group_id'),

        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'title_surname' => 'Title and Surname',
            'date_of_birth' => 'Date of Birth',
            'nhs_number' => 'NHS Number',
            'address' => 'Address (incl. Post Code)',
            'postcode' => 'Post Code',
            'email' => 'Email',
            'telephone' => 'Telephone',
            'gender_id' => 'Gender',
            'ethnic_group_id' => 'Ethnic Group',
            'gp_name' => 'GP\'s Name',
            'gp_address' => 'GP\'s Address',
            'gp_telephone' => 'GP\'s Telephone',
        );
    }

    /**
     * @param \Patient $patient
     */
    protected function mapNamesFromPatient(\Patient $patient)
    {
        $this->title_surname = $patient->title . ' ' . $patient->last_name;
        $this->other_names = $patient->first_name;

    }

    /**
     * @param \Patient $patient
     */
    protected function mapGenderFromPatient(\Patient $patient)
    {
        $gender_string = $patient->getGenderString();
        $gender = \Gender::model()->findByAttributes(array('name' => $gender_string));
        if ($gender) {
            $this->gender_id = $gender->id;
        }
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
        $this->date_of_birth = $patient->dob;
        $this->nhs_number = $patient->getNhsnum();
        $this->address = $patient->getSummaryAddress(',');
        $this->email = $patient->getEmail();
        $this->telephone = $patient->getPrimary_phone();

        $this->mapNamesFromPatient($patient);
        $this->mapGenderFromPatient($patient);
        $this->ethnic_group_id = $patient->ethnic_group_id;

        if ($patient->gp) {
            $this->gp_name = $patient->gp->getFullName();
            $this->gp_address = $patient->gp->getLetterAddress(array('delimiter' => ',', 'patient' => $patient));
            $this->gp_telephone = $patient->practice->phone;
        }
    }

    /**
     * Use the stored values to make a decent stab at putting together the patient name in its normalised form.
     *
     * @return string
     */
    public function getCompleteName()
    {
        list($title, $surname) = explode(' ', $this->title_surname, 2);
        if (!$surname) {
            $surname = $title;
            $title = '';
        }
        return $title . ' ' . $this->other_names . ' ' . $surname;
    }

    /**
     * Return the element data
     * @return array
     */
    public function getStructuredDataForPrint()
    {
        $data = array(
            'patientName' => $this->title_surname,
            'otherNames' => $this->other_names,
            'patientDateOfBirth' => $this->date_of_birth,
            'nhsNumber' => $this->nhs_number,
            'gender' => $this->gender->name,
            'patientAddress' => $this->address,
            'patientEmail' => $this->email,
            'patientTel' => $this->telephone,
            'gpName' => $this->gp_name,
            'gpAddress' => $this->gp_address,
            'gpTel' => $this->gp_telephone,
        );

        // TODO: maybe try and clean this up a bit more
        if ($gender = $this->gender) {
            if (strtolower($gender->name) == 'male') {
                $gender_data = array('', 'X', '', '');
            } elseif (strtolower($gender->name) == 'female') {
                $gender_data = array('', '', '', 'X');
            }
        } else {
            $gender_data = array('', '', '', '');
        }

        if ($group = $this->ethnic_group) {
            $data['ethnicGroup_' . $group->code] = 'X';
        }

        $data['signatureName'] = $this->getCompleteName();

        $dob = ($this->date_of_birth) ? \Helper::convertMySQL2NHS('dob') : '';

        if (!empty($dob)) {
            $year_header = array_merge(array(''), str_split(date('Y', strtotime($dob))));
        } else {
            $year_header = array('', '', '', '', '');
        }

        list($first, $second) = explode(' ', $this->postcode, 2);

        $postcode_header = str_split($first);
        while (count($postcode_header) > 4) {
            array_pop($postcode_header);
        }
        while (count($postcode_header) < 4) {
            $postcode_header[] = ' ';
        }

        $space_holder = array('');
        $data['demographicSummaryTable'] = array(
            0 => array_merge($gender_data, $space_holder, $year_header, $space_holder, $space_holder, $postcode_header)
        );

        return $data;
    }
}