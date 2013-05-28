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

return array(
	'element1' => array(
		'id' => 1,
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
		'id' => 2,
		'event_id' => 2,
		'eye' => ElementOperation::EYE_LEFT,
		'total_duration' => 120,
		'consultant_required' => true,
		'anaesthetist_required' => false,
		'anaesthetic_type' => ElementOperation::ANAESTHETIC_TOPICAL,
		'overnight_stay' => false,
		'comments' => 'bar',
		'status' => ElementOperation::STATUS_SCHEDULED
	),
        'element3' => array(
		'id' => 3,
                'event_id' => 3,
                'eye' => ElementOperation::EYE_BOTH,
                'total_duration' => 90,
                'consultant_required' => true,
                'anaesthetist_required' => false,
                'anaesthetic_type' => ElementOperation::ANAESTHETIC_TOPICAL,
                'overnight_stay' => false,
                'comments' => 'qux',
                'status' => ElementOperation::STATUS_SCHEDULED
        ),
        'element4' => array(
		'id' => 4,
                'event_id' => 5,
                'eye' => ElementOperation::EYE_LEFT,
                'total_duration' => 120,
                'consultant_required' => true,
                'anaesthetist_required' => false,
                'anaesthetic_type' => ElementOperation::ANAESTHETIC_TOPICAL,
                'overnight_stay' => false,
                'comments' => 'zob',
                'status' => ElementOperation::STATUS_SCHEDULED
        ),
);
