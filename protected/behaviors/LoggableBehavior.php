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

class LoggableBehavior extends CActiveRecordBehavior {
	private $_oldattributes = array();

	public function afterSave($event) {
		try {
			$username = Yii::app()->user->Name;
			$userid = Yii::app()->user->id;
		} catch(Exception $e) {
			// If we have no user object, this must be a command line program
			$username = "NO_USER";
			$userid = null;
		}

		if(empty($username)) {
			$username = "NO_USER";
		}

		if(empty($userid)) {
			$userid = null;
		}

		$newattributes = $this->Owner->getAttributes();
		$oldattributes = $this->getOldAttributes();

		if (!$this->Owner->isNewRecord) {
			// Compare old and new
			foreach ($newattributes as $name => $value) {
				if (!empty($oldattributes)) {
					$old = $oldattributes[$name];
				} else {
					$old = '';
				}

				if ($value != $old) {
					$log=new AuditTrail();
					$log->old_value = $old;
					$log->new_value = $value;
					$log->action = 'CHANGE';
					$log->model = get_class($this->Owner);
					$log->model_id = $this->Owner->getPrimaryKey();
					$log->field = $name;
					$log->stamp = date('Y-m-d H:i:s');
					$log->user_id = $userid;

					$log->save();
				}
			}
		} else {
			$log=new AuditTrail();
			$log->old_value = '';
			$log->new_value = '';
			$log->action = 'CREATE';
			$log->model = get_class($this->Owner);
			$log->model_id = $this->Owner->getPrimaryKey();
			$log->field = 'N/A';
			$log->stamp = date('Y-m-d H:i:s');
			$log->user_id = $userid;

			$log->save();

			foreach ($newattributes as $name => $value) {
				$log=new AuditTrail();
				$log->old_value = '';
				$log->new_value = $value;
				$log->action = 'SET';
				$log->model = get_class($this->Owner);
				$log->model_id = $this->Owner->getPrimaryKey();
				$log->field = $name;
				$log->stamp = date('Y-m-d H:i:s');
				$log->user_id = $userid;
				$log->save();
			}

		}
	}

	public function afterDelete($event) {
		try {
			$username = Yii::app()->user->Name;
			$userid = Yii::app()->user->id;
		} catch(Exception $e) {
			$username = "NO_USER";
			$userid = null;
		}

		if(empty($username)) {
			$username = "NO_USER";
		}

		if(empty($userid)) {
			$userid = null;
		}

		$log=new AuditTrail();
		$log->old_value = '';
		$log->new_value = '';
		$log->action = 'DELETE';
		$log->model = get_class($this->Owner);
		$log->model_id = $this->Owner->getPrimaryKey();
		$log->field = 'N/A';
		$log->stamp = date('Y-m-d H:i:s');
		$log->user_id = $userid;
		$log->save();
	}

	public function afterFind($event) {
		// Save old values
		$this->setOldAttributes($this->Owner->getAttributes());
	}

	public function getOldAttributes() {
		return $this->_oldattributes;
	}

	public function setOldAttributes($value) {
		$this->_oldattributes=$value;
	}
}
