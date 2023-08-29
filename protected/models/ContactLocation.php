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

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "contact_location".
 *
 * The followings are the available columns in table 'contact_location':
 *
 * @property int $id
 * @property Institution $institution_id
 * @property Contact $contact_id
 * @property Site $site_id
 */
class ContactLocation extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return ContactLocation the static model class
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
        return 'contact_location';
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id', 'safe', 'on' => 'search'),
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
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    /**
     * @return array customized attribute locations (name=>location)
     */
    public function attributeLocations()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $object = $this->site ? $this->site : $this->institution;

        $return = $object->name;

        if ($object->contact && $object->contact->address) {
            $return .= ', '.$object->contact->address->address1.', '.$object->contact->address->city;
        }

        return $return;
    }

    /**
     * gets a letter address for this contact location.
     *
     * @param unknown $params
     *
     * @return array() - address elements
     */
    public function getLetterAddress($params = array())
    {
        $owner = $this->site ? $this->site : $this->institution;
        if (@$params['contact']) {
            $contactRelation = @$params['contact'];
            $contact = $owner->$contactRelation;
        } else {
            $contact = $owner->contact;
        }

        $address = $contact->address;

        $res = $this->formatLetterAddress($this->contact, $address, $params);

        return $res;
    }

    public function getLetterArray($include_country)
    {
        $address = $this->site ? $this->site->contact->address : $this->institution->contact->address;
        $name = $this->site ? $this->site->correspondenceName : $this->institution->name;
        if (!is_array($name)) {
            $name = array($name);
        }

        return array_merge($name, $address->getLetterArray($include_country));
    }

    public function getPatients()
    {
        $criteria = new CDbCriteria();
        $criteria->join = 'join patient_contact_assignment on patient_contact_assignment.patient_id = `t`.id';
        $criteria->compare('location_id', $this->id);

        return Patient::model()->findAll($criteria);
    }
}
