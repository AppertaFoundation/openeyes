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

class ParallelMigration extends CDbMigration {
	public $canFork = false;
	public $threads = 8;
	public $pids = array();
	public $lockFP;

	public function checkForkPossible() {
		if (function_exists("pcntl_fork")) {
			Yii::app()->db->setActive(false);
			$pid = pcntl_fork();
			if ($pid == 0) {
				exit;
			}
			Yii::app()->db->setActive(true);

			if ($pid != -1) {
				$this->canFork = true;
				echo "********************************************************************************************\n";
				echo "* Looks like pcntl_fork() is available, so we will parallelise this migration to save time *\n";
				echo "********************************************************************************************\n";
			} else {
				echo "********************************************************************************************\n";
				echo "* Looks like pcntl_fork() is available but a test fork failed so we won't be parallelising *\n";
				echo "********************************************************************************************\n";
			}
		}
	}

	public function fork() {
		Yii::app()->db->setActive(false);
		$pid = pcntl_fork();
		Yii::app()->db->setActive(true);
		if ($pid >0) {
			$this->pids[] = $pid;
		}
		return $pid;
	}

	public function waitForThreads() {
		foreach ($this->pids as $pid) {
			pcntl_waitpid($pid,$status);
		}
		$this->pids = array();
	}

	public function parallelise($method, $data) {
		if (!$this->canFork) {
			$this->$method($data);
		} else {
			$workload = array();
			for ($i=0;$i<$this->threads;$i++) {
				$workload[$i] = array();
			}

			$n=0;
			foreach ($data as $item) {
				$workload[$n++][] = $item;
				if ($n >= $this->threads) {
					$n = 0;
				}
			}

			$ok = true;
			foreach ($workload as $i => $data) {
				$pid = $this->fork();
				if ($pid == 0) {
					$this->$method($data);
					exit;
				} else if ($pid == -1) {
					$ok = false;
				}
			}

			$this->waitForThreads();

			if (!$ok) {
				echo "\nOne or more fork() calls failed, migration aborted.\n";
				exit;
			}
		}
	}

	public function obtainLock() {
		$class = get_class($this);
		$this->lockFP = fopen("/tmp/.$class.lock","a+");
		flock($this->lockFP,LOCK_EX);
	}

	public function releaseLock() {
		fclose($this->lockFP);
		$class = get_class($this);
		@unlink("/tmp/.$class.lock");
	}
}
