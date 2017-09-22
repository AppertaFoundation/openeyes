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
 * This is the model class for table "ophcotherapya_decisiontreenodeoutcome".
 *
 * An outcome is an endpoint for a decision tree. The label is what is displayed to the user, the type is a coded value for use
 * in the system to make decisions about functional behaviour.
 *
 * @property int $id The outcome id
 * @property string $label The displayed label for this outcome
 * @property string $datatype The coded data type of this outcome
 **/
class OphCoTherapyapplication_DecisionTreeNode_ResponseType extends BaseActiveRecordVersioned
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
        return 'ophcotherapya_decisiontreenode_responsetype';
    }

    public function validRuleComparators()
    {
        if ($this->datatype == 'int') {
            return array('eq', 'lt', 'lte', 'gt', 'gte');
        } else {
            return array('eq');
        }
    }

    public function ruleLimit()
    {
        if (in_array($this->datatype, array('int', 'str'))) {
            return;
        } elseif ($this->datatype == 'bool') {
            return 1;
        }
        // TODO: implement for choice 'ch' response type
    }

    /**
     * return choice options for response types that have them
     * Note that this is hard coded for now to reduce admin overhead etc
     * if the response types expand greatly, then it will be worth expanding
     * this functionality to have choices defined in models.
     *
     * @param string $datatype
     *
     * @return array $choices
     */
    public function getChoices($datatype = null)
    {
        if ($datatype == null) {
            $datatype = $this->datatype;
        }

        if ($datatype == 'bool') {
            return array(
                '0' => 'No',
                '1' => 'Yes',
            );
        } elseif ($datatype == 'va') {
            return OphCoTherapyapplication_Helper::getInstance()->getVaListForForm();
        }

        return;
    }

    /**
     * translate a given response value into a text value for display.
     *
     * @param $value
     *
     * @return string
     */
    public function getDisplayValueforResponse($value)
    {
        switch ($this->datatype) {
            case 'bool':
                return $value ? 'Yes' : 'No';
                break;
            default:
                return $value;
        }
    }
}
