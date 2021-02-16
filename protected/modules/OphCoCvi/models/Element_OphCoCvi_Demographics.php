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
 * @property string $la_name
 * @property string $la_address
 * @property string $la_telephone
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
                'event_id, title_surname, other_names, date_of_birth, address, postcode, email, telephone, gender_id, '
                . 'ethnic_group_id, nhs_number, gp_name, gp_address, gp_telephone, la_name, la_address, la_telephone',
                'safe'
            ),
            array(
                'title_surname', 'length', 'max' => 120
            ),
            array(
                'other_names', 'length', 'max' => 100
            ),
            array(
                'postcode', 'length', 'max' => 4
            ),
            array(
                'email, gp_name, la_name', 'length', 'max' => 255
            ),
            array(
                'telephone, gp_telephone, la_telephone', 'length', 'max' => 20
            ),
            array(
                'telephone, gp_telephone, la_telephone', 'OEPhoneNumberValidator'
            ),
            array(
                'title_surname, other_names, date_of_birth, address, postcode, telephone, gender_id, ethnic_group_id, '
                . 'nhs_number, gp_name, gp_address, gp_telephone, la_name, la_address, la_telephone',
                'required',
                'on' => 'finalise'
            ),
            array('date_of_birth', 'OEDateValidatorNotFuture'),
            array('email','email'),
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
            'nhs_number' => \SettingMetadata::model()->getSetting('nhs_num_label').' Number',
            'address' => 'Address (incl. Post Code)',
            'postcode' => 'Post Code (1st half)',
            'email' => 'Email',
            'telephone' => 'Telephone',
            'gender_id' => 'Gender',
            'ethnic_group_id' => 'Ethnic Group',
            'gp_name' => \SettingMetadata::model()->getSetting('gp_label').'\'s Name',
            'gp_address' => \SettingMetadata::model()->getSetting('gp_label').'\'s Address',
            'gp_telephone' => \SettingMetadata::model()->getSetting('gp_label').'\'s Telephone',
            'la_name' => 'Local Authority Name',
            'la_address' => 'Local Authority Address',
            'la_telephone' => 'Local Authority Telephone',
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
        $this->address = $patient->getSummaryAddress(",\n");
        if ($patient->contact && $patient->contact->address) {
            $this->postcode = substr($patient->contact->address->postcode, 0, 4);
        }
        $this->email = $patient->getEmail();
        $this->telephone = $patient->getPrimary_phone();

        $this->mapNamesFromPatient($patient);
        $this->mapGenderFromPatient($patient);
        $this->ethnic_group_id = $patient->ethnic_group_id;

        if ($patient->gp) {
            $this->gp_name = $patient->gp->getFullName();
            $this->gp_address = $patient->gp->getLetterAddress(array('delimiter' => ",\n", 'patient' => $patient));
            if ($practice = $patient->practice) {
                $this->gp_telephone = $practice->phone;
            }
        }
    }

    /**
     * Use the stored values to make a decent stab at putting together the patient name in its normalised form.
     *
     * @return string
     */
    public function getCompleteName()
    {
        $name = array();

        if ($this->other_names) {
            $name[] = $this->other_names;
        }

        if ($parts = explode(' ', $this->title_surname, 2)) {
            if (count($parts) == 1) {
                $name[] = $parts[0];
            } else {
                array_unshift($name, $parts[0]);
                $name[] = $parts[1];
            }
        }

        return implode(' ', $name);
    }

    /**
     * @return array
     */
    protected function generateStructuredGenderHeader()
    {
        $gender_data = array_fill(0, 4, '');

        if ($gender = $this->gender) {
            if (strtolower($gender->name) == 'male') {
                $gender_data[1] = 'X';
            } elseif (strtolower($gender->name) == 'female') {
                $gender_data[3] = 'X';
            }
        }

        return $gender_data;
    }

    /**
     * @return array
     */
    protected function generateStructuredYearHeader()
    {
        if ($this->date_of_birth) {
            $year_header = array_merge(array(''), str_split(date('Y', strtotime($this->date_of_birth))));
        } else {
            $year_header = array('', '', '', '', '');
        }

        return $year_header;
    }

    /**
     * @return array
     */
    protected function generateStructuredPostcodeHeader()
    {
        $postcode_header = array_fill(0, 4, '');

        if ($this->postcode) {
            $parts = explode(' ', $this->postcode, 2);
            $postcode_header = str_split($parts[0]);

            // make sure correct length
            while (count($postcode_header) > 4) {
                array_pop($postcode_header);
            }
            while (count($postcode_header) < 4) {
                $postcode_header[] = '';
            }
        }

        return $postcode_header;
    }

    /**
     * @return array
     */
    protected function generateStructuredSummaryTable()
    {
        $gender_data = $this->generateStructuredGenderHeader();
        $year_header = $this->generateStructuredYearHeader();
        $postcode_header = $this->generateStructuredPostcodeHeader();

        $space_holder = array('');
        return array(
            0 => array_merge($gender_data, $space_holder, $year_header, $space_holder, $space_holder, $postcode_header)
        );
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
            'patientAddress' => \Helper::lineLimit($this->address, 1, 0, "\n", ''),
            'patientEmail' => $this->email,
            'patientTel' => $this->telephone,
            'gpName' => $this->gp_name,
            'gpAddress' => \Helper::lineLimit($this->gp_address, 1, 0, "\n", ''),
            'gpTel' => $this->gp_telephone,
            'localAuthorityName' => $this->la_name,
            'localAuthorityAddress' => \Helper::lineLimit($this->la_address, 1, 0, "\n", ''),
            'localAuthorityTel' => $this->la_telephone,
        );

        if ($group = $this->ethnic_group) {
            $data['ethnicGroup' . $group->code] = 'X';
        }

        $data['signatureName'] = $this->getCompleteName();

        $data['demographicSummaryTable'] = $this->generateStructuredSummaryTable();

        return $data;
    }

    public function getContainer_form_view()
    {
        return '//patient/element_container_form_no_bin';
    }
}
