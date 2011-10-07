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
// | section_by_firm_id | int(10) unsigned | NO   | MUL | NULL	  |		   |
// | display_order	| int(10) unsigned | YES  |	| NULL	  |		   |
// | firm_id		| int(10) unsigned | NO   | MUL | NULL	  |		   |

return array(
	'phraseByFirm1' => array(
		'phrase_name_id' => 39,
		'phrase' => 'Thanks for referring this [age] old [sub] who I saw today...',
		'section_id' => 1,
		'display_order' => 1,
		'firm_id' => 1,
	),
	'phraseByFirm2' => array(
		'phrase_name_id' => 40,
		'phrase' => 'I saw this [age] old [sub] as an emergency',
		'section_id' => 1,
		'display_order' => 2,
		'firm_id' => 1,
	),
	'phraseByFirm3' => array(
		'phrase_name_id' => 1,
		'phrase' => 'Testing',
		'section_id' => 2,
		'display_order' => 3,
		'firm_id' => 1,
	),
);