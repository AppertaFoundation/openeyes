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

// | id			| int(10) unsigned | NO   | PRI | NULL	  | auto_increment |
// | name		| varchar(255)	   | YES  |	| NULL	  |		   |
// | phrase		| text		   | YES  |	| NULL	  |		   |
// | section_id | int(10) unsigned | NO   | MUL | NULL	  |		   |

return array(
	'phrase1' => array(
		'phrase_name_id' => 1,
		'phrase' => 'Test phrase one',
		'section_id' => 1,
		'display_order' => 1,
		'firm_id' => 1,
	),
	'phrase2' => array(
		'phrase_name_id' => 2,
		'phrase' => 'Test phrase two',
		'section_id' => 1,
		'display_order' => 2,
		'firm_id' => 1,
	),
	'phrase3' => array(
		'phrase_name_id' => 3,
		'phrase' => 'Test phrase three',
		'section_id' => 2,
		'display_order' => 3,
		'firm_id' => 1,
	),
);