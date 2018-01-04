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
 * This is the model class for table "et_ophtroperationbooking_operation_preop_assessment_rule".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $parent_rule_id
 * @property int $theatre_id
 * @property int $subspecialty_id
 * @property bool $show_warning
 */
class OphTrOperationbooking_Admission_Letter_Warning_Rule extends BaseTree
{
    public $textFields = array('ruleType', 'site', 'firm', 'theatre', 'subspecialty', 'is_child', 'show_warning', 'warning_text', 'emphasis', 'strong');
    public $textFieldsDropdown = array('ruleType', 'site', 'firm', 'theatre', 'subspecialty', 'is_child', 'show_warning', 'warning_text', 'emphasis', 'strong');

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
        return 'ophtroperationbooking_admission_letter_warning_rule';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('rule_type_id, parent_rule_id, rule_order, site_id, theatre_id, subspecialty_id, is_child, show_warning, warning_text, emphasis, strong, firm_id', 'safe'),
            array('rule_type_id', 'required'),
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'children' => array(self::HAS_MANY, 'OphTrOperationbooking_Admission_Letter_Warning_Rule', 'parent_rule_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'theatre' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Theatre', 'theatre_id'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'ruleType' => array(self::BELONGS_TO, 'OphTrOperationbooking_Admission_Letter_Warning_Rule_Type', 'rule_type_id'),
            'parent' => array(self::BELONGS_TO, 'OphTrOperationbooking_Admission_Letter_Warning_Rule', 'parent_rule_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'rule_type_id' => 'Rule type',
            'parent_rule_id' => 'Parent',
            'rule_order' => 'Rule order',
            'site_id' => 'Site',
            'firm_id' => Firm::contextLabel(),
            'subspecialty_id' => 'Subspecialty',
            'theatre_id' => 'Theatre',
            'is_child' => 'Is child',
            'show_warning' => 'Show warning',
            'warning_text' => 'Warning text',
            'emphasis' => 'Italics',
            'strong' => 'Bold',
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

    public static function getRule($rule_type_name, $site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id)
    {
        if (!$rule_type = OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->find('name=?', array($rule_type_name))) {
            throw new Exception("We were asked for a rule type that doesn't exist: $rule_type_name");
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition("parent_rule_id is null and rule_type_id = $rule_type->id");
        $criteria->addCondition("rule_type_id = $rule_type->id");
        $criteria->order = 'rule_order asc';

        foreach (self::model()->findAll($criteria) as $rule) {
            if ($rule->applies($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id)) {
                return $rule->parse($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id);
            }
        }
    }

    public function applies($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id)
    {
        foreach (array('site_id', 'is_child', 'theatre_id', 'subspecialty_id', 'firm_id') as $field) {
            if ($this->{$field} !== null && $this->{$field} != ${$field}) {
                return false;
            }
        }

        return true;
    }

    public function parse($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id)
    {
        foreach ($this->children as $rule) {
            if ($rule->applies($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id)) {
                return $rule->parse($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id);
            }
        }

        return $this;
    }

    public function getWarning_text_TreeText()
    {
        if ($this->warning_text) {
            return substr($this->warning_text, 0, 55).' ...';
        }

        return '';
    }

    public function getIs_child_TreeText()
    {
        return $this->is_child ? 'C' : 'A';
    }

    public function getShow_warning_TreeText()
    {
        return $this->show_warning ? 'SHOW' : 'HIDE';
    }

    public function getEmphasis_TreeText()
    {
        return $this->emphasis ? 'I' : null;
    }

    public function getStrong_TreeText()
    {
        return $this->strong ? 'B' : null;
    }
}
