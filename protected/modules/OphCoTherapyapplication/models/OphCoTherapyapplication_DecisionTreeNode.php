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
 * This is the model class for table "ophcotherapya_decisiontreenode".
 *
 * Each node is a question for a given assessment flow. If it's parent is null, it is the root node
 * for the related flow. There should only be one with a null parent for each flow.
 *
 * @property int $id The node id
 * @property int $decisiontree_id The id of the assessment flow that this node is for
 * @property int $parent_id The id of the node's parent (if it has one)
 * @property string $question The question that this node is asking, if relevant
 * @property int $outcome_id If this is an outcome node, the id of the outcome it represents
 * @property string $default_function The name of the function that should be used to determine the default response to this node
 * @property string $default_value The default value that should be set for this node (if no default function selected)
 * @property string $response_type The response type for this node (va - value, ch - choice)
 * @property OphCoTherapyapplication_DecisionTree $decisiontree
 * @property OphCoTherapyapplication_DecisionTreeNode $parent
 * @property OphCoTherapyapplication_DecisionTreeOutcome $outcome
 **/
class OphCoTherapyapplication_DecisionTreeNode extends BaseActiveRecordVersioned
{
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
        return 'ophcotherapya_decisiontreenode';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'decisiontree' => array(self::BELONGS_TO, 'OphCoTherapyapplication_DecisionTree', 'decisiontree_id'),
            'parent' => array(self::BELONGS_TO, 'OphCoTherapyapplication_DecisionTreeNode', 'parent_id'),
            'outcome' => array(self::BELONGS_TO, 'OphCoTherapyapplication_DecisionTreeOutcome', 'outcome_id'),
            'rules' => array(self::HAS_MANY, 'OphCoTherapyapplication_DecisionTreeNodeRule', 'node_id'),
            'response_type' => array(self::BELONGS_TO, 'OphCoTherapyapplication_DecisionTreeNode_ResponseType', 'response_type_id'),
            'children' => array(self::HAS_MANY, 'OphCoTherapyapplication_DecisionTreeNode', 'parent_id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('question, outcome_id, default_function, default_value, response_type_id', 'safe'),
                array('outcome', 'outcomeValidation'),
                array('question, response_type_id', 'requiredIfNotOutcomeValidation'),
                array('default_function', 'defaultsValidation'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, question, outcome_id, default_function, default_value, response_type_id', 'safe', 'on' => 'search'),
        );
    }

    public function getDefaultFunctions()
    {
        return array('bestVisualAcuityForEye');
    }

    /*
     * Can only have children for question nodes, not outcomes
     */
    public function canAddChild()
    {
        if ($this->outcome) {
            return false;
        }
        if ($this->response_type->datatype == 'bool') {
            if (count($this->children) >= 2) {
                return false;
            }
        }

        return true;
    }

    /*
     * @return bool - whether a rule can be added to this node or not.
     */
    public function canAddRule()
    {
        // if it's the root node, there are no rules to define for it.
        if (!$this->parent) {
            return false;
        }

        // check the parent response type, and the number of rules already extant
        // if there is room for another one, return true
        if ($this->parent && $limit = $this->parent->response_type->ruleLimit()) {
            if ($limit <= count($this->rules)) {
                return false;
            }
        }

        return true;
    }

    /*
     * Works out a full abstract definition of the node.
     *
     * @return array - associative array of details of the node
     */
    public function getDefinition()
    {
        // return a definition of the node
        $defn = array();
        $defn['id'] = $this->id;
        $defn['data-type'] = $this->response_type ? $this->response_type->datatype : null;
        $defn['question'] = $this->question;
        $defn['outcome_id'] = $this->outcome_id;
        $defn['parent_id'] = $this->parent_id;
        $defn['rules'] = array();
        foreach ($this->rules as $rule) {
            $defn['rules'][] = $rule->getDefinition();
        }

        return $defn;
    }

    /*
     * Works out the default value for this node, based on the provided $patient
     *
     * @return string default response value
     */
    public function getDefaultValue($side, $patient, $episode)
    {
        if ($this->default_value) {
            return $this->default_value;
        } elseif ($this->default_function && $episode) {
            // call the appropriate default function
            return $this->{$this->default_function}($side, $patient, $episode);
        } else {
            return;
        }
    }

    /*
    * outcome being set implies that no other attributes should be set for the node
    */
    public function outcomeValidation($attribute)
    {
        if ($this->outcome_id && ($this->question || $this->default_function || $this->default_value || $this->response_type)) {
            $this->addError($attribute, 'Outcome nodes cannot have any other values set.');
        }
    }

    /*
     * if outcome is null then it implies this node should be a question node
     */
    public function requiredIfNotOutcomeValidation($attribute)
    {
        if (!$this->outcome_id && !$this->$attribute) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' required if not an outcome node.');
        }
    }

    /*
     * can only have one source for the default response for the node
     */
    public function defaultsValidation($attribute)
    {
        if ($this->default_function && $this->default_value) {
            $this->addError($attribute, 'Cannot have two default values for node response');
        }
    }

    /*
     * returns the best visual acuity record for the given the $side of the given $patient
     *
     * @return integer $visualacuity
     */
    public function bestVisualAcuityForEye($side, $patient, $episode)
    {
        if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
            return ($best = $api->getBestVisualAcuity($patient, $side, false)) ? $best->value : null;
        }

        return;
    }
}
