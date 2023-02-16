<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "address".
 *
 * The followings are the available columns in table 'address':
 *
 * @property int $id
 * @property int $contact_id ID of contact this address applies to
 * @property string $type Type of address (H = Home, C = Correspondence, T = Temporary)
 * @property string $date_start Date address is valid from
 * @property string $date_end Date address is valid to
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $postcode
 * @property string $county
 * @property int $country_id
 *
 * The following are the available model relations:
 * @property Country $country
 */
class Address extends BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Address the static model class
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
        return 'address';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('address1, address2, city, county', 'length', 'max' => 255),
            array('address1, city, postcode', 'required','on' => array('manage_practice')),
            array('postcode', 'length', 'max' => 10),
            array('country_id, address_type_id, date_start, date_end', 'safe'),
            array('contact_id, country_id', 'required'),
            array('id, address1, address2, city, postcode, county, country_id, address_type_id, date_start, date_end', 'safe', 'on' => 'search'),
            array('city', 'cityValidator'),
            array('address1, address2, city, county, postcode', 'filter', 'filter' => function ($value) {
                return strip_tags($value);
            }),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'country' => array(self::BELONGS_TO, 'Country', 'country_id'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'type' => array(self::BELONGS_TO, 'AddressType', 'address_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'postcode' => 'Postcode',
            'county' => \SettingMetadata::model()->getSetting('county_label'),
            'country_id' => 'Country',
            'address_type_id' => 'Address Type',
        );
    }

    /**
     * Is this a current address (not expired or in the future)?
     *
     * @return bool
     */
    public function isCurrent()
    {
        return (!$this->date_start || strtotime($this->date_start) <= time()) && (!$this->date_end || strtotime($this->date_end) >= time());
    }

    /**
     * @param bool $include_country
     *
     * @return string Address as formatted HTML (<br/> separated)
     */
    public function getLetterHtml($include_country = true)
    {
        return implode('<br />', $this->getLetterArray($include_country));
    }

    /**
     * @param bool $include_country
     *
     * @return string Address as formatted with line breaks
     */
    public function getLetterFormatted($include_country = true, $name = false)
    {
        return implode("\n", $this->getLetterArray($include_country, $name));
    }

    /**
     * @param bool $include_country
     *
     * @return string Address as text (, separated)
     */
    public function getLetterLine($include_country = true)
    {
        return implode(', ', $this->getLetterArray($include_country));
    }

    /**
     * @return string First line of address in a dropdown friendly form
     */
    public function getSummary()
    {
        return str_replace("\n", ', ', $this->address1);
    }

    /**
     * @return array Address as an array
     */
    public function getLetterArray($include_country = true, $name = false)
    {
        $address = array();

        if ($name) {
            $address = array($name);
        }

        $tempAddress = null;
        $linecount = 0;
        foreach (array('address1', 'address2', 'city', 'county', 'postcode') as $field) {
            if (!empty($this->$field) && trim($this->$field) != ',' && trim($this->$field) != "") {
                $line = $this->$field;
                $addressParts = explode("\n", $line);
                if ((string)SettingMetadata::model()->getSetting('correspondence_address_force_city_state_postcode_on_same_line') === "on") {
                    foreach ($addressParts as $part) {
                        if ($tempAddress === null) {
                            $tempAddress = $part . "\n";
                        } elseif ($field === 'city') {
                            $tempAddress .= $part;
                        } elseif ($field === 'county' || $field === 'postcode') {
                            $tempAddress .= ', ' . $part;
                        } else {
                            $tempAddress .= $part . "\n";
                        }
                    }
                } elseif (($newlines_setting = SettingMetadata::model()->getSetting('correspondence_address_max_lines')) >= 0) {
                    foreach ($addressParts as $part) {
                        if ($linecount == 0 && $tempAddress == null){
                            $tempAddress = $part;
                        }
                        elseif ($linecount <= $newlines_setting) {
                            $tempAddress = $tempAddress . "\n" . $part;
                        } else {
                            $tempAddress = $tempAddress .', ' . $part;
                        }
                    }
                }
                else{
                    $line = trim($line);
                    if ($field == 'address1' || $field == 'address2') {
                        foreach (explode("\n", $line) as $part) {
                            $part = trim($part);
                            if ($part != null && $part != ',' && trim($part) != '') {
                                $address[] = $part;
                            }
                        }
                    } else {
                        if ($line != null && $line != ',' && $line != '') {
                            $address[] = $line;
                        }
                    }
                }
            }
        }
        // add the tempAddress to address[] if setting set to not -1 (if we have a value in tempAddress)
        if ($tempAddress !== null &&
            ((SettingMetadata::model()->getSetting('correspondence_address_max_lines')) >= 0) ||
            (string)SettingMetadata::model()->getSetting('correspondence_address_force_city_state_postcode_on_same_line') === "on") {
            foreach (explode("\n", $tempAddress) as $part) {
                $part = trim($part);
                if ($part != null &&  $part != ',' && $part != '') {
                    $address[] = $part;
                }
            }
        }
        if ($include_country) {
            if (!empty($this->country->name)) {
                $site = null;
                // CConsoleApplication can't access the session
                if (\Yii::app() instanceof \CWebApplication) {
                    $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
                }

                if (!$site || ($site->institution->contact->address->country_id != $this->country_id)) {
                    $address[] = $this->country->name;
                }
            }
        }

        return $address;
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
        $criteria->compare('address1', $this->address1, true);
        $criteria->compare('address2', $this->address2, true);
        $criteria->compare('city', $this->city, true);
        $criteria->compare('postcode', $this->postcode, true);
        $criteria->compare('county', $this->county, true);
        $criteria->compare('country_id', $this->country_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord && !$this->address_type_id) {
                // make correspondence the default address type
                $this->address_type_id = AddressType::CORRESPOND;
                $this->address1 = str_replace("\T\\", "&", $this->address1);
                $this->address2 = str_replace("\T\\", "&", $this->address2);
                $this->city = str_replace("\T\\", "&", $this->city);
            }

            return true;
        }

        return false;
    }

    public function cityValidator($attribute, $param)
    {
        if (isset($this->city)) {
            if (1 === preg_match('~[0-9]~', $this->city)) {
                $this->addError($attribute, "City has Numeric values");
            }
        }
    }

    public function getDefaultCountryId()
    {
        $default_country_setting = SettingMetadata::model()->getSetting('default_country');
        return Country::model()->find('name = ?', [$default_country_setting])->id;
    }

    public function beforeValidate()
    {
        if ($this->date_start == "") {
            $this->date_start = null;
        }

        if ($this->date_end == "") {
            $this->date_end = null;
        }
        $this->date_start = Helper::convertNHS2MySQL($this->date_start);
        $this->date_end = Helper::convertNHS2MySQL($this->date_end);
        return parent::beforeValidate();
    }
}
