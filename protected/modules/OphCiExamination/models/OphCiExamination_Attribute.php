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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "ophciexamination_attribute".
 *
 * @property int $id
 * @property string $name
 * @property string $label
 * @property OphCiExamination_AttributeElement[] $attribute_elements
 */
class OphCiExamination_Attribute extends \BaseActiveRecordVersioned
{
    protected $attribute_options = array();

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_Attribute the static model class
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
        return 'ophciexamination_attribute';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name', 'required'),
                array('id, name, label', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'attribute_elements_id' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_AttributeElement', 'attribute_id'),
                'attribute_elements' => array(self::MANY_MANY, 'ElementType', 'ophciexamination_attribute_element(attribute_id,element_type_id)'),
        );
    }

    /**
     * Fetches all the attributes for an element_type filtered by subspecialty
     * Options are stashed in attribute_options property for easy iteration.
     *
     * @param int  $element_type_id
     * @param int  $subspecialty_id
     * @param bool $include_descendents
     *
     * @return OphCiExamination_Attribute[]
     */
    public function findAllByElementAndSubspecialty($element_type_id, $subspecialty_id = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->select = 't.*';
        $criteria->distinct = true;
        $element_type_ids = array($element_type_id);

        $criteria->addInCondition('attribute_element.element_type_id', $element_type_ids);
        if ($subspecialty_id) {
            $criteria->addCondition('t.subspecialty_id = :subspecialty_id OR t.subspecialty_id IS NULL');
            $criteria->addCondition('t.id NOT IN (SELECT exclude.option_id FROM ophciexamination_attribute_option_exclude exclude where subspecialty_id = :subspecialty_id)');
            $criteria->params[':subspecialty_id'] = $subspecialty_id;
        } else {
            $criteria->addCondition('subspecialty_id IS NULL');
        }
        $criteria->join = 'JOIN ophciexamination_attribute_element attribute_element ON attribute_element.id = t.attribute_element_id
							JOIN ophciexamination_attribute attribute ON attribute_element.attribute_id = attribute.id';
        $criteria->order = 'attribute.display_order,attribute_element.attribute_id,t.display_order,t.id';
        $all_attribute_options = OphCiExamination_AttributeOption::model()->findAll($criteria);

        $attributes = array();
        $attribute = null;
        $attribute_options = array();
        foreach ($all_attribute_options as $attribute_option) {
            if (!$attribute || $attribute->id != $attribute_option->attribute_element->attribute_id) {
                if ($attribute) {
                    $attribute->attribute_options = array_values($attribute_options);
                    $attribute_options = array();
                    $attributes[] = $attribute;
                }
                $attribute = $attribute_option->attribute_element->attribute;
            }
            $attribute_options[] = $attribute_option;
        }
        if ($attribute) {
            $attribute->attribute_options = array_values($attribute_options);
            $attributes[] = $attribute;
        }

        return $attributes;
    }

    public function getAttributeOptions()
    {
        return $this->attribute_options;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Attribute Name',
            'label' => 'Attribute Label',
            'element_type.name' => 'Element Mapping',
            'attribute_elements.name' => 'Element Mapping',
        );
    }
}
