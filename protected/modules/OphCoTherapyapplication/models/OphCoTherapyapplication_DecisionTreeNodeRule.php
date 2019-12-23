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
 * This is the model class for table "ophcotherapya_decisiontreenoderule".
 *
 * Each rule applies to its specified node to determine if that node is the next point in the decision tree,
 * based on the response given for its parent node. More than one rule may apply for a given node (to allow specific ranges)
 *
 * @property int $id The rule id
 * @property int $node_id The id of the node this rule applies to
 * @property string $parent_check The comparison operator to check against the parent response, to determine if this node is the one that is displayed
 * @property string $parent_check_value The value that should be used in conjunction with the $parent_check comparison operator
 * @property OphCoTherapyapplication_DecisionTreeNode $node
 **/
class OphCoTherapyapplication_DecisionTreeNodeRule extends BaseActiveRecordVersioned
{
    public $COMPARATORS = array(
            'eq' => '=',
            'lt' => '<',
            'gt' => '>',
            'lte' => '<=',
            'gte' => '>=',
    );

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
        return 'ophcotherapya_decisiontreenoderule';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'node' => array(self::BELONGS_TO, 'OphCoTherapyapplication_DecisionTreeNode', 'node_id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('parent_check, parent_check_value', 'safe'),
                array('node_id, parent_check, parent_check_value', 'required'),
                // TODO add rule to check that the value matches the response type of the node this rule belongs to
        );
    }

    public function displayParentCheck()
    {
        return CHtml::encode(@$this->COMPARATORS[$this->parent_check]);
    }

    /**
     * generate the display value of the parent check value on this rule.
     *
     * @return string display value
     */
    public function displayParentCheckValue()
    {
        if (get_class($this->node->parent->response_type) === 'OphCoTherapyapplication_DecisionTreeNode_ResponseType') {
            $choices = $this->node->parent->response_type->getChoices();
            if ($choices) {
                if (array_key_exists($this->parent_check_value, $choices)) {
                    return $choices[$this->parent_check_value];
                }
            }
        }

        // default to just displaying the check value
        return $this->parent_check_value;
    }

    /*
     * Works out a full abstract definition of the rule.
    *
    * @return array - associative array of details of the rule
    */
    public function getDefinition()
    {
        return array(
            'parent_check' => $this->parent_check,
            'parent_check_value' => $this->parent_check_value,
        );
    }

    public function checkValue($val)
    {
        //TODO: cast $val and $this->parent_check_val to the same datatype for accurate comparisons
        switch ($this->parent_check) {
            case 'eq':
                return $val == $this->parent_check_val;
                break;
            case 'lt':
                return $val < $this->parent_check_val;
                break;
            case 'gt';

                return $val > $this->parent_check_val;
                break;
            case 'lte':
                return $val <= $this->parent_check_value;
                break;
            case 'gte':
                return $val >= $this->parent_check_value;
                break;
        }
    }
}
