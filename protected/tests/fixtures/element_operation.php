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
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

return array(
	'element1' => array(
		'event_id' => 1,
		'eye' => ElementOperation::EYE_BOTH,
		'total_duration' => 90,
		'consultant_required' => true,
		'anaesthetist_required' => false,
		'anaesthetic_type' => ElementOperation::ANAESTHETIC_TOPICAL,
		'overnight_stay' => false,
		'comments' => 'foo',
		'status' => ElementOperation::STATUS_SCHEDULED
	),
	'element2' => array(
		'event_id' => 2,
		'eye' => ElementOperation::EYE_LEFT,
		'total_duration' => 120,
		'consultant_required' => true,
		'anaesthetist_required' => false,
		'anaesthetic_type' => ElementOperation::ANAESTHETIC_TOPICAL,
		'overnight_stay' => false,
		'comments' => 'bar',
		'status' => ElementOperation::STATUS_SCHEDULED
	)
);
