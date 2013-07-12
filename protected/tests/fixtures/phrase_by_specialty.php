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

// | id                      | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
// | name                    | varchar(255)     | YES  |     | NULL    |                |
// | phrase                  | text             | YES  |     | NULL    |                |
// | section_by_specialty_id | int(10) unsigned | NO   | MUL | NULL    |                |
// | display_order           | int(10) unsigned | YES  |     | NULL    |                |
// | specialty_id            | int(10) unsigned | NO   | MUL | NULL    |                |

return array(
	'phraseBySpecialty1' => array(
		'phrase_name_id' => 7,
		'phrase' => 'drug use',
		'section_id' => 10,
		'display_order' => 0,
		'specialty_id' => 8,
	),
	'phraseBySpecialty2' => array(
		'phrase_name_id' => 8,
		'phrase' => 'alcoholism',
		'section_id' => 10,
		'display_order' => 0,
		'specialty_id' => 8,
	),
	'phraseBySpecialty3' => array(
		'phrase_name_id' => 9,
		'phrase' => 'Loss of vision',
		'section_id' => 1,
		'display_order' => 0,
		'specialty_id' => 8,
	),
	'phraseBySpecialty4' => array(
		'phrase_name_id' => 10,
		'phrase' => 'Peripheral field loss',
		'section_id' => 1,
		'display_order' => 1,
		'specialty_id' => 8,
	),
	'phraseBySpecialty5' => array(
		'phrase_name_id' => 11,
		'phrase' => 'Distortion of vision',
		'section_id' => 1,
		'display_order' => 2,
		'specialty_id' => 8,
	),
	'phraseBySpecialty6' => array(
		'phrase_name_id' => 12,
		'phrase' => 'Central vision disturbance',
		'section_id' => 1,
		'display_order' => 3,
		'specialty_id' => 8,
	),
);
