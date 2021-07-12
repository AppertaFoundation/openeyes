<?php


namespace OEModule\OphCoCvi\models;

use PatientIdentifierHelper;

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
                'event_id, title_surname, other_names, date_of_birth, address, postcode, postcode_2nd, email, telephone, gender_id, '
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
                'postcode, postcode_2nd, gp_postcode, gp_postcode_2nd, la_postcode, la_postcode_2nd', 'filter', 'filter'=>'trim'
            ),
            array(
                'postcode, postcode_2nd, gp_postcode, gp_postcode_2nd, la_postcode, la_postcode_2nd', 'length', 'max' => 4 ,  
            ),
            
            array(
                'email, gp_name, la_name', 'length', 'max' => 255
            ),
            array(
                'telephone, gp_telephone, la_telephone', 'length', 'max' => 20
            ),
            array(
                'title_surname, other_names, date_of_birth, address, postcode, telephone, gender_id, ethnic_group_id, '
                . 'nhs_number, gp_name, gp_address, gp_telephone, la_name, la_address, la_telephone',
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
            'postcode' => 'Post Code (1st half)',
            'postcode_2nd' => 'Post Code (2nd half)',
            'gp_postcode' => 'GP Post Code (1st half)',
            'email' => 'Email',
            'telephone' => 'Telephone',
            'gender_id' => 'Gender',
            'ethnic_group_id' => 'Ethnic Group',
            'gp_name' => 'GP\'s Name',
            'gp_address' => 'GP\'s Address',
            'gp_telephone' => 'GP\'s Telephone',
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
        //$this->nhs_number = $patient->getNhsnum();
        $this->nhs_number = PatientIdentifierHelper::getIdentifierValue($patient->globalIdentifier);
        $this->address = $patient->getSummaryAddress(",\n");
        
        if ($patient->contact && $patient->contact->address) {
            $postcode = explode(" ", \Helper::setPostCodeFormat($patient->contact->address->postcode));
            
            $this->postcode = $postcode[0];
            $this->postcode_2nd = $postcode[1];
        }
        $this->email = $patient->getEmail();
        $this->telephone = $patient->getPrimary_phone();

        $this->mapNamesFromPatient($patient);
        $this->mapGenderFromPatient($patient);
        $this->ethnic_group_id = $patient->ethnic_group_id;

        if ($patient->gp) {
            $this->gp_name = $patient->gp->getFullName();
            $this->gp_address = $patient->gp->getLetterAddress(array('delimiter' => ",\n", 'patient' => $patient));
            
            $gpPostcode = explode(" ", \Helper::setPostCodeFormat( $patient->gp->getGPPostcode(array('patient' => $patient))));
            
            $this->gp_postcode = $gpPostcode[0];
            $this->gp_postcode_2nd = $gpPostcode[1];
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
            }
            else {
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
        $gender_data = array_fill(0,4, '');

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
        $postcode_header = array_fill(0,4,'');

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
            'patientAddress' => \Helper::lineLimit($this->address,1, 0, "\n", ''),
            'patientEmail' => $this->email,
            'patientTel' => $this->telephone,
            'gpName' => $this->gp_name,
            'gpAddress' => \Helper::lineLimit($this->gp_address,1, 0, "\n", ''),
            'gpTel' => $this->gp_telephone,
            'localAuthorityName' => $this->la_name,
            'localAuthorityAddress' => \Helper::lineLimit($this->la_address,1, 0, "\n", ''),
            'localAuthorityTel' => $this->la_telephone,
        );

        if ($group = $this->ethnic_group) {
            $data['ethnicGroup' . $group->code] = 'X';
        }

        $data['signatureName'] = $this->getCompleteName();

        $data['demographicSummaryTable'] = $this->generateStructuredSummaryTable();

        return $data;
    }
    
    /*
     * Get elements for CVI PDF
     * 
     * @return array
     */
    public function getElementsForCVIpdf()
    {
        
        $nhsNum = preg_replace('/[^0-9]/', '', $this->nhs_number);

        switch($this->gender_id){
            case 2:
                $sex = 0;
                break;
            case 1:
                $sex = 1;
                break;
            default:
                $sex = 2;
        }
        
        
        $patientAddress = $this->getAddressFormatForPDF( $this->address );
        $gpAddress = $this->getAddressFormatForPDF( $this->gp_address );
        $laAddress = $this->getAddressFormatForPDF( $this->la_address );
        
        $elements = [
            'Title_Surname' => $this->title_surname,
            'All_other_names' => $this->other_names,
            'Address1' => $patientAddress['address1'],
            'Address2' => $patientAddress['address2'],
            'Postcode1' => $this->postcode,
            'Postcode2' => $this->postcode_2nd,
            'Telephone' => $this->telephone,
            'Email' => $this->email,
            'DoB' => \Helper::convertMySQL2NHS($this->date_of_birth),
            'Sex' => $sex,
            'NHS_1' => substr($nhsNum, 0, 3),
            'NHS_2' => substr($nhsNum, 3, 3),
            'NHS_3' => substr($nhsNum, 6, 4),
            'GP_name' => $this->gp_name,            
            'GP_Address' => $gpAddress['address1'],         
            'GP_Address_Line_2' => $gpAddress['address2'],  
            'GP_postcode_1' => $this->gp_postcode,      
            'GP_postcode_2' => $this->gp_postcode_2nd,      
            'GP_Telephone' => $this->gp_telephone,     
            'Council_Name' => $this->la_name,      
            'Council_Address' => $laAddress['address1'],      
            'Council_Address2' => $laAddress['address2'],      
            'Council_Postcode1' => $this->la_postcode,       
            'Council_Postcode2' => $this->la_postcode_2nd,      
            'Council_Telephone' => $this->la_telephone,       
            'Ethnicity' => $this->ethnic_group_id - 1,          //Values: 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16, "Off", "Yes"
            
        ];
        
        return $elements;
    }
   
}