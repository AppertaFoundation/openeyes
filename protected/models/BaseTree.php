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
 * Base tree class.
 *
 */
class BaseTree extends BaseActiveRecordVersioned
{
    public $textFields = array();
    public $textFieldsDropdown = array();

    public function findAllAsTree($parent = null, $first = true, $text = 'text', $institution_id = null)
    {
        $tree = array();
        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_rule_id <=> :parent_rule_id');
        $criteria->params[':parent_rule_id'] = $parent->id ?? null;
        $criteria->order = 'rule_order asc';
        if ($institution_id) {
            $criteria->with = 'institutions';
            $criteria->addCondition('institutions_institutions.institution_id = :institution_id');
            $criteria->params[':institution_id'] = $institution_id;
        }

        if ($first && $parent) {
            $treeItem = array(
                'id' => $parent->id,
                'text' => $parent->$text,
                'children' => $this->findAllAsTree($parent, false, $text, $institution_id),
            );
            $treeItem['hasChildren'] = !empty($treeItem['children']);

            $tree[] = $treeItem;
        } else {
            $class = get_class($this);
            foreach ($class::model()->findAll($criteria) as $rule) {
                $treeItem = array(
                    'id' => $rule->id,
                    'text' => $rule->$text,
                    'children' => $this->findAllAsTree($rule, false, $text, $institution_id),
                );
                $treeItem['hasChildren'] = !empty($treeItem['children']);

                $tree[] = $treeItem;
            }
        }

        return $tree;
    }

    public function expandTextFields($fields)
    {
        $text = '';

        foreach ($fields as $key => $value) {
            $target = is_int($key) ? $value : $key;
            $prefix = is_int($key) ? '' : $value.': ';

            if ($this->$target !== null) {
                if ($text) {
                    $text .= ' ';
                }
                if (is_object($this->$target)) {
                    $param = method_exists($this->$target, 'getTreeName') ? 'treeName' : 'name';
                    $textItem = $this->$target->$param;
                    $textItem !== '' && $text .= $prefix.'['.$textItem.']';
                } else {
                    $param = method_exists($this, 'get'.ucfirst($target).'_TreeText') ? $target.'_TreeText' : $target;
                    $textItem = $this->$param;
                    $textItem !== '' && $text .= $prefix.'['.$textItem.']';
                }
            }
        }

        return $text;
    }

    public function getTextPlain()
    {
        return $this->expandTextFields($this->textFields);
    }

    public function getTreeName()
    {
        $text = $this->rule_order.': '.$this->expandTextFields($this->textFieldsDropdown);

        $parents = 0;
        $object = $this;

        while ($object->parent_rule_id) {
            ++$parents;
            $object = $object->parent;
        }

        return str_repeat('+ ', $parents).$text;
    }

    public function getText()
    {
        return $this->rule_order.': '.CHtml::openTag('a', array('href' => '#', 'id' => 'item'.$this->id, 'class' => 'treenode')).$this->textPlain.CHtml::closeTag('a')." <a href=\"#\" rel=\"$this->id\" class=\"addTreeItemHere\" ><img style=\"height:20px\" alt=\"Add tree item here\" src=\"".Yii::app()->assetManager->createUrl('img/_elements/btns/plus-sign.png')."\" /></a>\n";
    }

    public function getListAsTree($parent = null, $institution_id = null)
    {
        $list = array();

        $criteria = new CDbCriteria();
        $criteria->addCondition('parent_rule_id <=> :parent');
        $criteria->params[':parent'] = $parent->id ?? null;
        $criteria->order = 'rule_order asc';
        if ($institution_id) {
            $criteria->addCondition('institution_id = :institution_id');
            $criteria->params[':institution_id'] = $institution_id;
        }

        $class = get_class($this);
        foreach ($class::model()->findAll($criteria) as $rule) {
            $list[] = $rule;

            foreach ($this->getListAsTree($rule) as $child) {
                $list[] = $child;
            }
        }

        return $list;
    }

    public function delete()
    {
        if ($this->children) {
            foreach ($this->children as $child) {
                if (!$child->delete()) {
                    return false;
                }
            }
        }

        return parent::delete();
    }
}
