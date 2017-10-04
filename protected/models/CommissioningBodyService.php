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
 * This is the model class for table "commissioning_body_service".
 *
 * The followings are the available columns in table 'commissioning_body_service':
 *
 * @property int $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Contact $contact
 * @property CommissioningBodyServiceType $type
 * @property Practice[] $practices
 */
class CommissioningBodyService extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return CommissioningBody the static model class
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
        return 'commissioning_body_service';
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
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name, code, commissioning_body_service_type_id, commissioning_body_id, contact_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
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
            'type' => array(self::BELONGS_TO, 'CommissioningBodyServiceType', 'commissioning_body_service_type_id'),
            // At this stage, there is a one to many relationship for bodies to services, but at some point in the future
            // it may be necessary to update this to a many to many to relationship
            'commissioning_body' => array(self::BELONGS_TO, 'CommissioningBody', 'commissioning_body_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'commissioning_body_id' => 'Commissioning body',
            'commissioning_body_service_type_id' => 'Service type',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Return the type short name for this service.
     *
     * @return string
     */
    public function getTypeShortName()
    {
        return $this->type ? $this->type->shortname : 'CBS';
    }

    /**
     * Ensure we have a contact/address before returning.
     *
     * @return Address|null
     */
    public function getAddress()
    {
        if ($this->contact && $this->contact->address) {
            return $this->contact->address;
        }
    }

    /**
     * Returns the appropriate name (first portion of address) for the service.
     *
     * @return array
     */
    public function getCorrespondenceName()
    {
        $cname = array($this->name);
        if ($static_type_name = $this->type->correspondence_name) {
            $cname[] = $static_type_name;
        }

        return $cname;
    }
}
