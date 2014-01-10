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
 * +----+---------------------------+--------------------------------+-----------------------+---------------------+-----------------+---------------------+---------------+---------------+---------+------------------------+----------+
  | id | name                      | class_name                     | last_modified_user_id | last_modified_date  | created_user_id | created_date        | event_type_id | display_order | default | parent_element_type_id | required |
  +----+---------------------------+--------------------------------+-----------------------+---------------------+-----------------+---------------------+---------------+---------------+---------+------------------------+----------+
  |  1 | History                   | ElementHistory                 |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  2 | Past History              | ElementPastHistory             |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  3 | Visual function           | ElementVisualFunction          |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  4 | Visual acuity             | ElementVisualAcuity            |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  5 | Mini-refraction           | ElementMiniRefraction          |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  6 | Visual fields             | ElementVisualFields            |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  7 | Extraocular movements     | ElementExtraocularMovements    |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  8 | Cranial nervers           | ElementCranialNervers          |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  |  9 | Orbital examination       | ElementOrbitalExamination      |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 10 | Anterior segment          | ElementAnteriorSegment         |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 11 | Anterior segment drawing  | ElementAnteriorSegmentDrawing  |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 12 | Gonioscopy                | ElementGonioscopy              |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 13 | intraocular pressure      | ElementIntraocularPressure     |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 14 | Posterior segment         | ElementPosteriorSegment        |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 15 | Posterior segment drawing | ElementPosteriorSegmentDrawing |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 16 | Conclusion                | ElementConclusion              |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  | 17 | POH                       | ElementPOH                     |                     1 | 1900-01-01 00:00:00 |               1 | 1900-01-01 00:00:00 |             1 |             1 |       1 |                   NULL |     NULL |
  +----+---------------------------+--------------------------------+-----------------------+---------------------+-----------------+---------------------+---------------+---------------+---------+------------------------+----------+

 */
return array(
	 'history' => array(
		'name' => 'History',
		'class_name' => 'BaseEventTypeElement',
		'event_type_id' => $this->getRecord('event_type', 'examination')->id,
		'display_order' => 1,
		'id' => 1,
		'default' => 1,
	 ),
	 'pasthistory' => array(
		  'name' => 'Past History',
		  'class_name' => 'BaseEventTypeElement',
		  'event_type_id' => $this->getRecord('event_type', 'examination')->id,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
		  //'parent_element_type_id' => $this->getRecord('element_type', 'history')->id,
	 ),
	 'visualfunction' => array(
		  'name' => 'Visual function',
		  'class_name' => 'BaseEventTypeElement',
		  'event_type_id' => $this->getRecord('event_type', 'examination')->id,
		  'display_order' => 3,
	 ),
	 'va' => array(
		  'name' => 'Visual acuity',
		  'class_name' => 'BaseEventTypeElement',
		  'event_type_id' => $this->getRecord('event_type', 'examination')->id,
		  'display_order' => 4,
	 ),
	/*
	 'elementType5' => array(
		  'name' => 'Mini-refraction',
		  'class_name' => 'ElementMiniRefraction',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType6' => array(
		  'name' => 'Visual fields',
		  'class_name' => 'ElementVisualFields',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType7' => array(
		  'name' => 'Extraocular movements',
		  'class_name' => 'ElementExtraocularMovements',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType8' => array(
		  'name' => 'Cranial nervers',
		  'class_name' => 'ElementCranialNervers',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType9' => array(
		  'name' => 'Orbital examination',
		  'class_name' => 'ElementOrbitalExamination',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType10' => array(
		  'name' => 'Anterior segment',
		  'class_name' => 'ElementAnteriorSegment',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType11' => array(
		  'name' => 'Anterior segment drawing',
		  'class_name' => 'ElementAnteriorSegmentDrawing',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType12' => array(
		  'name' => 'Gonioscopy',
		  'class_name' => 'ElementGonioscopy',
		  'event_type_id' => 1,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType13' => array(
		  'name' => 'intraocular pressure',
		  'class_name' => 'ElementIntraocularPressure',
		  'event_type_id' => 1,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType14' => array(
		  'name' => 'Posterior segment',
		  'class_name' => 'ElementPosteriorSegment',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType15' => array(
		  'name' => 'Posterior segment drawing',
		  'class_name' => 'ElementPosteriorSegmentDrawing',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType16' => array(
		  'name' => 'Conclusion',
		  'class_name' => 'ElementConclusion',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 ),
	 'elementType17' => array(
		  'name' => 'POH',
		  'class_name' => 'ElementPOH',
		  'event_type_id' => 4,
		  'display_order' => 1,
		  'parent_element_type_id' => 1,
	 )
	*/
);
