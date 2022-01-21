<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

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
 * @property string $la_email
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
    const GENDER_MALE = 5;
    const GENDER_FEMALE = 6;
    const GENDER_UNSPECIFIED = 7;

    const PDF_ETHNIC_GROUP_MAPPING = [
        1 => 0,
        2 => 1,
        3 => 2,
        4 => 3,
        5 => 4,
        6 => 5,
        7 => 6,
        8 => 7,
        9 => 8,
        10 => 9,
        11 => 10,
        12 => 12,
        13 => 11,
        14 => 13,
        15 => 15,
    ];

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
                . 'ethnic_group_id, nhs_number, gp_name, gp_address, gp_telephone, la_name, la_address, la_telephone, la_email, describe_ethnics',
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
                'email, gp_name, la_name, la_email', 'length', 'max' => 255
            ),
            array(
                'telephone, gp_telephone, la_telephone', 'length', 'max' => 20
            ),
            array(
                'title_surname, other_names, date_of_birth, address, postcode, postcode_2nd, telephone, gender_id, '
                . 'nhs_number, gp_name, gp_address, gp_telephone, gp_postcode, gp_postcode_2nd, la_name, la_address, la_telephone, la_postcode, la_postcode_2nd',
                'required',
                'on' => 'finalise'
            ),
          //  array('describe_ethnics', 'ethnicsDescribeValidation', 'required', 'on' => 'finalise'),
            array('date_of_birth', 'OEDateValidatorNotFuture'),
            array('la_email', 'email', 'on' => 'finalise', 'allowEmpty' => true),
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
            'postcode_2nd' => '',
            'email' => 'Email',
            'telephone' => 'Telephone',
            'gender_id' => 'Sex',
            'ethnic_group_id' => 'Ethnic Group',
            'gp_name' => 'GP\'s Name',
            'gp_address' => 'GP\'s Address',
            'gp_postcode' => 'GP\'s Post Code',
            'gp_postcode_2nd' => '',
            'gp_telephone' => 'GP\'s Telephone',
            'la_name' => 'Local Authority Name',
            'la_address' => 'Local Authority Address',
            'la_postcode' => 'Local Authority Post Code',
            'la_postcode_2nd' => '',
            'la_telephone' => 'Local Authority Telephone',
            'la_email' => 'Local Authority Email',
            'describe_ethnics' => 'Describe other ethnic group'
        );
    }

    /**
     * Validate other ethnics textarea if the ethnic group describe_needs == 1
     * @param type $attribute
     * @param type $params
     */
    public function ethnicsDescribeValidation($attribute, $params)
    {
        $ethnic = \EthnicGroup::model()->findByAttributes(array('id' => $this->ethnic_group_id));
        if ($ethnic) {
            if (($ethnic->describe_needs === "1") && !(bool) preg_match('/\S/', $this->describe_ethnics)) {
                $this->addError($attribute, 'Describe other ethnics cannot be blank');
            }
        }
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
            switch ($gender->id) {
                case 1:
                    $this->gender_id = self::GENDER_MALE;
                    break;
                case 2:
                    $this->gender_id = self::GENDER_FEMALE;
                    break;
                case 3:
                case 4:
                    $this->gender_id = self::GENDER_UNSPECIFIED;
                    break;
            }
        }
    }

    private function getEthnicIdForCVI(\Patient $patient)
    {
        if ($patient->ethnic_group_id) {
            $ethnic = \EthnicGroup::model()->findByAttributes(array('id_assignment' => $patient->ethnic_group_id));
            if ($ethnic) {
                $this->ethnic_group_id = $ethnic->id;
            }
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
        $this->nhs_number = PatientIdentifierHelper::getIdentifierValue($patient->globalIdentifier);
        $this->address = $patient->getSummaryAddress(",\n");

        if ($patient->contact && $patient->contact->address) {
            $postcode = explode(" ", \Helper::setPostCodeFormat($patient->contact->address->postcode));

            $this->postcode = array_key_exists(0, $postcode) ? $postcode[0] : null;
            $this->postcode_2nd = array_key_exists(1, $postcode) ? $postcode[1] : null;
        }
        $this->email = $patient->getEmail();
        $this->telephone = $patient->getPrimary_phone();

        $this->mapNamesFromPatient($patient);
        $this->mapGenderFromPatient($patient);

        $this->getEthnicIdForCVI( $patient );

        if ($patient->gp) {
            $this->gp_name = $patient->gp->getFullName();
            $this->gp_address = $patient->gp->getLetterAddress(array('delimiter' => ",\n", 'patient' => $patient));

            $gpPostcode = explode(" ", \Helper::setPostCodeFormat( $patient->gp->getGPPostcode(array('patient' => $patient))));

            $this->gp_postcode = array_key_exists(0, $gpPostcode) ? $gpPostcode[0] : null;
            $this->gp_postcode_2nd = array_key_exists(1, $gpPostcode) ? $gpPostcode[1] : null;
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

    /*
     * Get elements for CVI PDF
     *
     * @return array
     */
    public function getElementsForCVIpdf()
    {

        $nhsNum = preg_replace('/[^0-9]/', '', $this->nhs_number);

        switch ($this->gender_id) {
            case 5:
                $sex = 1;
                break;
            case 6:
                $sex = 0;
                break;
            default:
                $sex = 2;
                break;
        }

        $patientAddress = $this->getAddressFormatForPDF( $this->address );


        $otherEthnicity = $this->getOtherEthnicityForPDF();

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
            'dob_original' => $this->date_of_birth,
            'Sex' => $sex,
            'Sex_String' => $this->gender->name,
            'NHS_1' => substr($nhsNum, 0, 3),
            'NHS_2' => substr($nhsNum, 3, 3),
            'NHS_3' => substr($nhsNum, 6, 4),
            'Ethnicity' => $this->getEthnicityIdForPDF(),          //Values: 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16, "Off", "Yes"
            'Other White background description' => $otherEthnicity[0],
            'Ather Mixed/Multiple ethnic background description' => $otherEthnicity[1],
            'Other Asian background, description' => $otherEthnicity[2],
            'Other Black/African/Caribbean background description' => $otherEthnicity[3],
            'Other Chinese background description' => $otherEthnicity[4],
            'Other ethnicity description' => $otherEthnicity[5],
            'EthnicityForVisualyImpaired' => $this->getEthnicityForVisualyImpaired(),
        ];

        return $elements;
    }

    private function getEthnicityIdForPDF()
    {
        if ($ethnic = \EthnicGroup::model()->findByAttributes(array('id' => $this->ethnic_group_id))) {
            return array_key_exists($ethnic->id_assignment, self::PDF_ETHNIC_GROUP_MAPPING) ?
                self::PDF_ETHNIC_GROUP_MAPPING[$ethnic->id_assignment] : "Off";
        }
        return 'Off';
    }

    private function getEthnicityForVisualyImpaired()
    {
        $result = array();
        $ethnics = \EthnicGroup::model()->findAll();

        foreach ($ethnics as $ethnic) {
            $result[] = [
                'id' => $ethnic['id'],
                'name' => $ethnic['name'],
                'describe_needs' => $ethnic['describe_needs'],
            ];
        }

        return $result;
    }

    private function getOtherEthnicityForPDF()
    {
        $result = [
            0 => '',
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => '',
        ];
        $ethnicityID = $this->getEthnicityIdForPDF();
        if (($ethnicityID >=0) && ($ethnicityID <= 2)) {
            $result[0] = $this->describe_ethnics;
        } elseif (($ethnicityID >=3) && ($ethnicityID <= 6)) {
            $result[1] = $this->describe_ethnics;
        } elseif (($ethnicityID >=7) && ($ethnicityID <= 10)) {
            $result[2] = $this->describe_ethnics;
        } elseif (($ethnicityID >=11) && ($ethnicityID <= 13)) {
            $result[3] = $this->describe_ethnics;
        } elseif (($ethnicityID >=14) && ($ethnicityID <= 15)) {
            $result[4] = $this->describe_ethnics;
        } else {
            $result[5] = $this->describe_ethnics;
        }

        return $result;
    }

    /**
     * Get address format for PDF, sliced 2 lines
     * @return array
     */
    public function getAddressFormatForPDF($address)
    {
        $patientAddress = explode(PHP_EOL, $address);
        $patientAddressLen = count($patientAddress);

        $Address1 = '';
        $Address2 = '';

        if ($patientAddressLen > 1) {
            foreach (array_slice($patientAddress, 0, $patientAddressLen / 2) as $value) {
                $Address1 .= $value;
            }
            foreach (array_slice($patientAddress, $patientAddressLen / 2) as $value) {
                $Address2 .= $value;
            }
        } else {
            $Address1 = $patientAddress[0];
        }

        return [
            'address1' => str_replace(array("\n","\r"), ' ', $Address1),
            'address2' => str_replace(array("\n","\r"), ' ', $Address2)
        ];
    }
}
