<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class WorklistDefinitionMapping.
 *
 * @property int $id
 * @property string $key
 * @property int $worklist_definition_id
 * @property int $display_order
 * @property WorklistDefinition $worklist_definition
 * @property WorklistDefinitionMappingValue[] $values
 */
class WorklistDefinitionMapping extends BaseActiveRecord
{
    /**
     * Convenience variable for storing the string representation of the mapping values.
     *
     * @var string
     */
    private $_valuelist;

    /**
     * Abstraction for getting instance of class.
     *
     * @param $class
     *
     * @return mixed
     */
    protected function getInstanceForClass($class, $args = array())
    {
        if (empty($args)) {
            return new $class();
        }

        $cls = new ReflectionClass($class);

        return $cls->newInstanceArgs($args);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist_definition_mapping';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('worklist_definition_id, key', 'required'),
            array('display_order', 'numerical', 'integerOnly' => true, 'allowEmpty' => true),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, worklist_definition_id, key', 'safe', 'on' => 'search'),
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
            'worklist_definition' => array(self::BELONGS_TO, 'WorklistDefinition', 'worklist_definition_id'),
            'values' => array(self::HAS_MANY, 'WorklistDefinitionMappingValue', 'worklist_definition_mapping_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'willdisplay' => 'Will Be Displayed',
            'valuelist' => 'Matched Values',
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
        $criteria->compare('worklist_definition_id', $this->worklist_definition_id, true);
        $criteria->compare('key', $this->key, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Convenience getter for managing a string representation of the set of mapped values.
     *
     * @return string
     */
    public function getValueList()
    {
        if (!isset($this->_valuelist)) {
            $res = array();
            foreach ($this->values as $v) {
                $res[] = $v->mapping_value;
            }

            $this->_valuelist = implode(',', $res);
        }

        return $this->_valuelist;
    }

    /**
     * Convenience setter for managing a string representation of the set of mapped values.
     *
     * Note that this does not affect the actual related models for this item, so care should be taken around
     * keeping them in sync where necessary
     *
     * @param $valuelist
     */
    public function setValueList($valuelist)
    {
        if (is_array($valuelist)) {
            $valuelist = implode(',', $valuelist);
        }

        $this->_valuelist = $valuelist;
    }

    /**
     * @param array $values
     *
     * @throws CDbException
     * @throws Exception
     *
     * @return bool
     */
    public function updateValues($values = array())
    {
        $kept = array();
        foreach ($this->values as $mv) {
            if (!in_array($mv->mapping_value, $values)) {
                if (!$mv->delete()) {
                    throw new Exception("Could not delete value {$mv->mapping_value}");
                }
            } else {
                $kept[] = $mv->mapping_value;
            }
        }

        foreach ($values as $val) {
            if (!in_array($val, $kept)) {
                $mv = $this->getInstanceForClass('WorklistDefinitionMappingValue');
                $mv->worklist_definition_mapping_id = $this->id;
                $mv->mapping_value = $val;
                if (!$mv->save()) {
                    throw new Exception('Could not save mapping value'.print_r($mv->getErrors(), true));
                }
            }
        }

        $this->setValueList($values);

        return true;
    }

    /**
     * @return bool
     */
    public function getWillDisplay()
    {
        return !is_null($this->display_order);
    }
}
