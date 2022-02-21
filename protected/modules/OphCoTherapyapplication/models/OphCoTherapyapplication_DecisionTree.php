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
 * This is the model class for table "ophcotherapya_decisiontree".
 *
 * Each decision tree can be used to answer a series of questions to arrive at OphCoTherapyapplication_DecisionTreeOutcome
 *
 * @property int $id The tree id
 * @property string $name The name of the tree, this is only used for administrative purposes to identify the tree.
 **/
class OphCoTherapyapplication_DecisionTree extends BaseActiveRecordVersioned
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
        return 'ophcotherapya_decisiontree';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'nodes' => array(self::HAS_MANY, 'OphCoTherapyapplication_DecisionTreeNode', 'decisiontree_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('name, institution_id', 'safe'),
                array('name, institution_id', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, name, institution_id', 'safe', 'on' => 'search'),
        );
    }

    public function getRootNode()
    {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('decisiontree_id' => $this->id, 'parent_id' => null));
        $node = OphCoTherapyapplication_DecisionTreeNode::model()->find($criteria);

        return $node;
    }

    public function getDefinition()
    {
        $definition = array();
        if ($root = $this->getRootNode()) {
            $definition['root_id'] = $root->id;
        }

        return $definition;
    }
}
