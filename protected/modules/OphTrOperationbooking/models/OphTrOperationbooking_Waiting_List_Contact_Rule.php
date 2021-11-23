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
 * This is the model class for table "ophtroperationbooking_waiting_list_contact_rule".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $parent_rule_id
 * @property int $site_id
 * @property int $service_id
 * @property int $firm_id
 * @property bool $is_child
 * @property string $name
 * @property string $telephone
 *
 * @property OphTrOperationbooking_Waiting_List_Contact_Rule[] $children
 * @property OphTrOperationbooking_Waiting_List_Contact_Rule $parent
 */
class OphTrOperationbooking_Waiting_List_Contact_Rule extends BaseTree
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'waiting_list_contact_rule_id';
    }

    public $textFields = array('site', 'service', 'firm', 'is_child', 'name', 'telephone');
    public $textFieldsDropdown = array('site', 'service', 'firm', 'is_child', 'name', 'telephone');

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $class_name
     * @return OphTrOperationbooking_Waiting_List_Contact_Rule the static model class
     */
    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationbooking_waiting_list_contact_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('parent_rule_id, rule_order, site_id, service_id, firm_id, is_child, name, telephone', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, parent_rule_id, site_id, service_id, firm_id, is_child, name, telephone', 'safe', 'on' => 'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'children' => array(self::HAS_MANY, 'OphTrOperationbooking_Waiting_List_Contact_Rule', 'parent_rule_id'),
            'parent' => array(self::BELONGS_TO, 'OphTrOperationbooking_Waiting_List_Contact_Rule', 'parent_rule_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'institutions' => array(self::MANY_MANY, 'Institution', 'ophtropbooking_waiting_list_contact_rule_institution(waiting_list_contact_rule_id, institution_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'parent_rule_id' => 'Parent',
            'rule_order' => 'Rule order',
            'institution_id' => 'Institution',
            'site_id' => 'Site',
            'firm_id' => Firm::contextLabel(),
            'service_id' => 'Service',
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

    public function applies($site_id, $service_id, $firm_id, $is_child)
    {
        foreach (array('site_id', 'service_id', 'firm_id', 'is_child') as $field) {
            if ($this->{$field} !== null && $this->{$field} !== ${$field}) {
                return false;
            }
        }

        return true;
    }

    public function parse($site_id, $service_id, $firm_id, $is_child)
    {
        foreach ($this->children as $child_rule) {
            if ($child_rule->applies($site_id, $service_id, $firm_id, $is_child)) {
                return $child_rule->parse($site_id, $service_id, $firm_id, $is_child);
            }
        }

        return $this;
    }

    public function getIs_child_TreeText()
    {
        return $this->is_child ? 'Child' : 'Adult';
    }
}
