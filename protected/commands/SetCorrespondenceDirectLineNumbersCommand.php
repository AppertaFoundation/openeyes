<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class SetCorrespondenceDirectLineNumbersCommand extends CConsoleCommand {
	public function run($args) {
		Yii::import('application.modules.OphCoCorrespondence.models.*');

		foreach (ElementLetter::model()->findAll() as $element_letter) {
			$event = Yii::app()->db->createCommand("select * from event where id = $element_letter->event_id")->queryRow();
			$episode = Yii::app()->db->createCommand("select * from episode where id = $event->episode_id")->queryRow();

			if ($dl = FirmSiteSecretary::model()->find('firm_id=? and site_id=?',array($episode['firm_id'],$element_letter->site_id))) {
				Yii::app()->db->createCommand("update et_ophcocorrespondence_letter set direct_line = '$dl->direct_line' where id = $element_letter->id")->query();
				echo ".";
			}
		}

		echo "\n";
	}
}
