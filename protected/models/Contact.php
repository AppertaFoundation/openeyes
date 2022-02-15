<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "contact".
 *
 * The following are the available columns in table 'contact':
 *
 * @property int $id
 * @property string $nick_name
 * @property string $primary_phone
 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property string $qualifications
 * @property string $comment
 * @property string $national_code
 * @property int $active
 * @property string $email
 *
 *
 * The following are the available model relations:
 * @property Gp $gp
 * @property Address[] $addresses
 * @property Address $address Primary address
 * @property Address $homeAddress Home address
 * @property Address $correspondAddress Correspondence address
 * @property ContactLabel $label
 *
 * The following are pseudo (calculated) fields
 * @property string $salutationName
 * @property string $fullName
 */
class Contact extends BaseActiveRecordVersioned
{
    public $source;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Contact the static model class
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
        return 'contact';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('nick_name', 'length', 'max' => 80),
            array('title, first_name, last_name, nick_name, primary_phone, qualifications, maiden_name,
             contact_label_id, active, comment, national_code, fax',
                'safe'),
            array('first_name, last_name', 'required', 'on' => array('manualAddPatient', 'referral', 'self_register', 'other_register', 'manage_gp')),
            array('title, maiden_name', 'match', 'pattern' => '/^[a-zA-Z]+(([\',. -][a-zA-Z -])?[a-zA-Z]*)*$/', 'message' => 'Invalid {attribute} entered.', 'except' => 'hscic_import'),
            array('first_name, last_name', 'parenthesisValidator'),
            array('first_name, last_name', 'required', 'on' => array('manage_gp_role_req')),
            array('contact_label_id', 'required', 'on' => array('manage_gp_role_req'), 'message' => 'Please select a Role.'),
            array('primary_phone', 'requiredValidator'),
            array('id, nick_name, primary_phone, title, first_name, last_name, qualifications, email', 'safe', 'on' => 'search'),
            array('first_name', 'required', 'on' => array('manage_practice')),
            array('first_name', 'length', 'max' => 300, 'on' => 'manage_practice'),
            array('primary_phone','OEPhoneNumberValidator'),
            array('email', 'length', 'max' => 255),
            array('email','email'),
            array('email', 'required', 'on' => array('self_register')),
            array('title, first_name, last_name, nick_name, primary_phone, qualifications, maiden_name,
                    national_code, fax, email', 'filter', 'filter' => function ($value) {
                return strip_tags($value);
            }),
        );
    }

    public function parenthesisValidator($attribute, $params)
    {
        $scenario = $this->getScenario();
        if ($scenario === 'hscic_import') {
            return;
        }
        if ($scenario === 'admin_contact') {
            // One of first name and last name needs to be entered
            if (!$this->first_name && !$this->last_name) {
                // to avoid duplicated warning messages
                if (!$this->getErrors('Empty Names')) {
                    $this->addError('Empty Names', "First Name and Last Name cannot be both blank");
                }
            } else {
                // if the first name and last name are from admin contact, allow alphabets, parenthesis, and dash (-)
                if ($this->$attribute && !preg_match("/^[a-zA-Z \(\)-]+$/", $this->$attribute)) {
                    $this->addError($attribute, "Invalid {$this->getAttributeLabel($attribute)} entered.");
                }
            }
        } else {
            // use the conventional regex for any other scenario
            if (!preg_match('/^[a-zA-Z]+(([\',. -][a-zA-Z -])?[a-zA-Z]*)*$/', $this->$attribute)) {
                $this->addError($attribute, "Invalid {$this->getAttributeLabel($attribute)} entered.");
            }
        }
    }
    public function requiredValidator($attribute, $params)
    {
        $scenario = $this->getScenario();
        if ($scenario === 'manualAddPatient' || $scenario === 'referral' || $scenario === 'self_register' || $scenario === 'other_register') {
            // add error based on the admin param and the attribute value.
            if ((Yii::app()->params['patient_phone_number_mandatory'] === '1') && !$this->primary_phone) {
                $this->addError($attribute, $this->getAttributeLabel($attribute) . ' cannot be blank.');
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'gp' => array(self::HAS_ONE, 'Gp', 'contact_id'),
            'addresses' => array(self::HAS_MANY, 'Address', 'contact_id'),
            // Prefer H records for primary address, but fall back to others
            'address' => array(
                self::HAS_ONE,
                'Address',
                'contact_id',
                'order' => '((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id,' . AddressType::HOME . ') DESC, date_start DESC',
            ),
            // Prefer H records for home address, but fall back to others
            'homeAddress' => array(
                self::HAS_ONE,
                'Address',
                'contact_id',
                'order' => '((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id,' . AddressType::HOME . ') DESC, date_start DESC',
            ),
            // Prefer C records for correspond address, but fall back to others
            'correspondAddress' => array(
                self::HAS_ONE,
                'Address',
                'contact_id',
                'order' => '((date_end is NULL OR date_end > NOW()) AND (date_start is NULL OR date_start < NOW())) DESC, FIELD(address_type_id,' . AddressType::CORRESPOND . ') DESC, date_start DESC',
            ),
            'label' => array(self::BELONGS_TO, 'ContactLabel', 'contact_label_id'),
            'locations' => array(self::HAS_MANY, 'ContactLocation', 'contact_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'nick_name' => 'Nickname',
            'primary_phone' => 'Phone number',
            'title' => 'Title',
            'first_name' => $this->scenario === 'manage_practice' ? 'Practice Name' : 'First name',
            'last_name' => 'Last name',
            'qualifications' => 'Qualifications',
            'contact_label_id' => 'Label',
            'email' => 'Email',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('nick_name', $this->nick_name, true);
        $criteria->compare('email', $this->email, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => 'ContactBehavior',
        );
    }

    /**
     * @return string Full name
     */
    public function getFullName()
    {
        return trim(implode(' ', array($this->title, $this->first_name, $this->last_name)));
    }

    public function getReversedFullName()
    {
        return trim(implode(' ', array($this->title, $this->last_name, $this->first_name)));
    }

    public function getCorrespondenceName()
    {
        return $this->getFullName();
    }

    /**
     * @return string Salutaion name
     */
    public function getSalutationName()
    {
        if ($this->title) {
            return $this->title . ' ' . $this->last_name;
        } else {
            return $this->first_name . ' ' . $this->last_name;
        }
    }

    public function contactLine($location = false)
    {
        $line = $this->fullName . ' (' . $this->label->name;
        if ($location) {
            $line .= ', ' . $location;
        }

        return $line . ')';
    }

    /**
     * Searches for contacts with the given criteria.
     *
     * string $term - string to search Contact last name - will do exact match so provide wild cards as required
     * string $label - the exact label string to match on.
     * boolean $exclude - if true, search for all contacts without the given label. otherwise only that label
     * string $join - table to join on to force only matches of that contact type. Currently only supports person
     */
    public function findByLabel($term, $label, $exclude = false, $join = null)
    {
        if (!$cl = ContactLabel::model()->find('name=?', array($label))) {
            throw new Exception("Unknown contact label: $label");
        }

        $contacts = array();

        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('lower(last_name)', $term, false);
        if ($exclude) {
            $criteria->compare('contact_label_id', '<>' . $cl->id);
        } else {
            $criteria->compare('contact_label_id', $cl->id);
        }

        $criteria->order = 'title, first_name, last_name';

        if ($join) {
            if (!in_array($join, array('person'))) {
                throw new Exception('Unknown join table ' . $join);
            }
            // force to only match on Person contacts
            $criteria->join = 'join ' . $join . ' model_join on model_join.contact_id = t.id';
        }
        $found_contacts = self::model()->with(array('locations' => array('with' => array('site', 'institution')), 'label'))->findAll($criteria);

        foreach ($found_contacts as $contact) {
            if ($contact->locations) {
                foreach ($contact->locations as $location) {
                    $contacts[] = array(
                        'line' => $contact->contactLine($location),
                        'contact_location_id' => $location->id,
                    );
                }
            } else {
                $contacts[] = array(
                    'line' => $contact->contactLine(),
                    'contact_id' => $contact->id,
                );
            }
        }

        return $contacts;
    }

    public function getType()
    {
        foreach (array('User', 'Gp', 'Patient', 'Person') as $model) {
            if ($model::model()->find('contact_id=?', array($this->id))) {
                return $model;
            }
        }

        return false;
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'gp-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function getActiveContacts($patient_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.active = 1');
        $criteria->join .= "JOIN contact_label cl ON cl.id = t.contact_label_id";
        $criteria->addCondition('cl.is_private = 0');
        return Contact::model()->with('label')->findAll($criteria);
    }
}
