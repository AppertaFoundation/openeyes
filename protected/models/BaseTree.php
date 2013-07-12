<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * Base class for all elements
 */
class BaseTree extends BaseActiveRecord
{
	public $textFields = array();
	public $textFieldsDropdown = array();

	public function findAllAsTree($parent=null, $first=true, $text='text')
	{
		$tree = array();
		$criteria = new CDbCriteria;
		$criteria->addCondition('parent_rule_id <=> :parent_rule_id');
		$criteria->params[':parent_rule_id'] = $parent ? $parent->id : null;
		$criteria->order = 'rule_order asc';

		if ($first && $parent) {
			$treeItem = array(
				'id' => $parent->id,
				'text' => $parent->$text,
				'children' => $this->findAllAsTree($parent,false,$text),
			);
			$treeItem['hasChildren'] = !empty($treeItem['children']);

			$tree[] = $treeItem;
		} else {
			$class = get_class($this);
			foreach ($class::model()->findAll($criteria) as $rule) {
				$treeItem = array(
					'id' => $rule->id,
					'text' => $rule->$text,
					'children' => $this->findAllAsTree($rule,false,$text),
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
				if ($text) $text .= ' ';
				if (is_object($this->$target)) {
					$param = method_exists($this->$target,'getTreeName') ? 'treeName' : 'name';
					$textItem = $this->$target->$param;
					strlen($textItem) >0 && $text .= $prefix.'['.$textItem.']';
				} else {
					$param = method_exists($this,'get'.ucfirst($target).'_TreeText') ? $target.'_TreeText' : $target;
					$textItem = $this->$param;
					strlen($textItem) >0 && $text .= $prefix.'['.$textItem.']';
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
			$parents++;
			$object = $object->parent;
		}

		return str_repeat('+ ',$parents).$text;
	}

	public function getText()
	{
		return $this->rule_order.': '.CHtml::openTag('a',array('href'=>'#','id'=>'item'.$this->id,'class'=>'treenode')).$this->textPlain.CHtml::closeTag('a')." <a href=\"#\" rel=\"$this->id\" class=\"addTreeItemHere\" ><img width=\"46px\" height=\"23px\" src=\"".Yii::app()->createUrl('/img/_elements/btns/plus-sign.png')."\" /></a>\n";
	}

	public function getListAsTree($parent=null)
	{
		$list = array();

		$criteria = new CDbCriteria;
		$criteria->addCondition('parent_rule_id <=> :parent');
		$criteria->params[':parent'] = $parent ? $parent->id : null;
		$criteria->order = 'rule_order asc';

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
