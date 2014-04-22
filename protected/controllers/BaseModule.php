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
 * A base module class
 */

class BaseModule extends CWebModule
{

	/**
	 * Returns the module parent class if it is also an installed module
	 *
	 * @return mixed
	 */
	protected function getInheritedModule()
	{
		$r = new ReflectionClass($this);
		$parent = $r->getParentClass();
		if ($id = Yii::app()->moduleAPI->moduleIDFromClass('\\'.$parent->name)) {
			return Yii::app()->getModule($id);
		}
	}

	private $_inheritance_list;

	/**
	 * Builds and returns the list of modules relevant to this specific module as defined by inheritance
	 *
	 * @return array
	 */
	public function getModuleInheritanceList()
	{
		$module = $this;
		if (!$this->_inheritance_list) {
			$this->_inheritance_list = array();
			while (method_exists($module, 'getInheritedModule') &&
					$module = $module->getInheritedModule()) {
				$this->_inheritance_list[] = $module;
			}
		}
		return $this->_inheritance_list;
	}
}
