<?php
/*
 _____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
*/

class FindOperationsThatNeedGAButAreInANonGASessionCommand extends CConsoleCommand
{
	public function getName() {
	}

	public function getHelp() {
	}

	public function run($args) {
		foreach (Yii::app()->db->createCommand()
			->select('element_operation.id')
			->from('element_operation')
			->join('booking','booking.element_operation_id = element_operation.id')
			->join('session','booking.session_id = session.id')
			->where('session.general_anaesthetic = 0')
			->queryAll() as $row) {
			echo $row['id']."\n";
		}
	}
}
?>
