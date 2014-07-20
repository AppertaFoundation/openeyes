<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class LookupTable extends CActiveRecordBehavior
{
	/*
	 * Named scope for rows that are active
	 */
	public function active()
	{
		$this->owner->getDbCriteria()->compare($this->owner->getTableAlias(true) . '.active', 1);
		return $this->owner;
	}

	/*
	 * Named scope for rows that are active or match the PK(s) given
	 *
	 * @param mixed $id PK
	 */
	public function activeOrPk($id)
	{
		$alias = $this->owner->getTableAlias(true);

		$crit = new CDbCriteria;
		$crit->compare("{$alias}.active", 1);
		$crit->compare($alias . "." . $this->owner->metadata->tableSchema->primaryKey, $id, false, 'OR');
		$this->owner->getDbCriteria()->mergeWith($crit);
		return $this->owner;
	}
}
