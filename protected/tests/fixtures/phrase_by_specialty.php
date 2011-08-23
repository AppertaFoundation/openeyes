<?php
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