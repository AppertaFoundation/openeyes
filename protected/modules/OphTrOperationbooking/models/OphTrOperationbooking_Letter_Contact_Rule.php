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
 * This is the model class for table "ophtroperationbooking_letter_contact_rule".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $site_id
 * @property int $subspecialty_id
 * @property int $theatre_id
 * @property int $firm_id
 * @property string $telephone
 *
 * The followings are the available model relations:
 * @property Site $site
 * @property Subspecialty $subspecialty
 * @property OphTrOperationbooking_Operation_Theatre $theatre
 * @property OphTrOperationbooking_Operation_Firm $firm
 */
class OphTrOperationbooking_Letter_Contact_Rule extends BaseTree
{
    public $textFields = array('site', 'firm', 'theatre', 'subspecialty', 'refuse_telephone' => 'refuse', 'refuse_title' => 'title', 'health_telephone' => 'health');
    public $textFieldsDropdown = array('site', 'firm', 'theatre', 'subspecialty', 'refuse_telephone' => 'refuse', 'health_telephone' => 'health');

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'ophtroperationbooking_letter_contact_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('parent_rule_id, site_id, subspecialty_id, theatre_id, firm_id, refuse_telephone, health_telephone, refuse_title, rule_order, is_child', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, site_id, subspecialty_id, theatre_id', 'safe', 'on' => 'search'),
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
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'children' => array(self::HAS_MANY, 'OphTrOperationbooking_Letter_Contact_Rule', 'parent_rule_id'),
            'parent' => array(self::BELONGS_TO, 'OphTrOperationbooking_Letter_Contact_Rule', 'parent_rule_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'parent_rule_id' => 'Parent rule',
            'rule_order' => 'Rule order',
            'site_id' => 'Site',
            'subspecialty_id' => 'Subspecialty',
            'firm_id' => Firm::contextLabel(),
            'theatre_id' => 'Theatre',
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

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    /**
     * @param $site_id
     * @param $subspecialty_id
     * @param $theatre_id
     * @param $firm_id
     * @param $is_child - is the patient a child?
     *
     * @return bool
     */
    public function applies($site_id, $subspecialty_id, $theatre_id, $firm_id, $is_child)
    {
        foreach (array('site_id', 'subspecialty_id', 'theatre_id', 'firm_id', 'is_child') as $field) {
            if ($this->{$field} !== null && $this->{$field} != ${$field}) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $site_id
     * @param $subspecialty_id
     * @param $theatre_id
     * @param $firm_id
     * @param $is_child - is the patient a child?
     *
     * @return $this
     */
    public function parse($site_id, $subspecialty_id, $theatre_id, $firm_id, $is_child)
    {
        foreach ($this->children as $child_rule) {
            if ($child_rule->applies($site_id, $subspecialty_id, $theatre_id, $firm_id, $is_child)) {
                return $child_rule->parse($site_id, $subspecialty_id, $theatre_id, $firm_id, $is_child);
            }
        }

        return $this;
    }
}
