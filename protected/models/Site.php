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
 * This is the model class for table "site".
 *
 * The followings are the available columns in table 'site':
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $postcode
 * @property string $fax
 * @property string $telephone
 *
 * The followings are the available model relations:
 * @property Institution $institution
 * @property Contact $contact
 * @property Contact $replyTo
 * @property ImportSource $import
 */
class Site extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Site the static model class
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
        return 'site';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.name');
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
            array('name, short_name, remote_id, telephone', 'required'),
            array('name', 'length', 'max' => 255),
            array('institution_id, name, remote_id, short_name, location_code fax, telephone, contact_id, replyto_contact_id, source_id, active', 'safe'),
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
            //'theatres' => array(self::HAS_MANY, 'Theatre', 'site_id'),
            //'wards' => array(self::HAS_MANY, 'Ward', 'site_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'replyTo' => array(self::BELONGS_TO, 'Contact', 'replyto_contact_id'),
            'import' => array(self::HAS_ONE, 'ImportSource', 'site_id'),
            'toLocation' => array(self::HAS_ONE, 'OphCoCorrespondence_InternalReferral_ToLocation', 'site_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'remote_id' => 'Code',
            'name' => 'Name',
            'institution_id' => 'Institution',
            'location_code' => 'Location Code',
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getListForCurrentInstitution($field = 'short_name')
    {
        $result = array();
        foreach (Institution::model()->getCurrent()->sites as $site) {
            $result[$site->id] = $site->$field;
        }

        return $result;
    }

    public function getLongListForCurrentInstitution()
    {
        $institution = Institution::model()->getCurrent();

        $display_query = SettingMetadata::model()->findByAttributes(array('key' => 'display_institution_name'));
        $display_institution = $display_query->getSettingName();

        $result = array();
        foreach ($institution->sites as $site) {
            $site_name = '';

            if ($institution->short_name && $site->name != 'Unknown') {
                if ($display_institution) {
                    $site_name = $institution->short_name.' at ';
                }
            }
            $site_name .= $site->name;

            if ($site->location) {
                $site_name .= ', '.$site->location;
            }

            $result[$site->id] = $site_name;
        }

        return $result;
    }

    public function getDefaultSite()
    {
        $site = null;
        if (Yii::app()->params['default_site_code']) {
            $site = $this->findByAttributes(array('code' => Yii::app()->params['default_site_code']));
        }
        if (!$site) {
            $site = $this->find();
        }

        return $site;
    }

    public function getCorrespondenceName()
    {
        if ($this->institution->short_name) {
            $display_query = SettingMetadata::model()->findByAttributes(array('key' => 'display_institution_name'));
            $display_institution = $display_query->getSettingName();

            if (!strstr($this->name, $this->institution->short_name)) {
                if ($display_institution  == 'Off') {
                    return $this->name;
                } else {
                    return $this->institution->short_name.' at '.$this->name;
                }
            }
        }

        // this avoids duplicating lines on the addresses
        if ($this->institution->name == $this->name) {
            return $this->name;
        }

        return array($this->institution->name, $this->name);
    }

    public function getShortname()
    {
        return $this->short_name ? $this->short_name : $this->name;
    }

    public function getReplyToAddress($params = array())
    {
        if ($contact = $this->replyTo) {
            $params['contact'] = 'replyTo';

            return $this->getLetterAddress($params);
        }
    }
}
