<?php
// | id                      | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
// | name                    | varchar(255)     | YES  |     | NULL    |                |
// | phrase                  | text             | YES  |     | NULL    |                |
// | section_by_specialty_id | int(10) unsigned | NO   | MUL | NULL    |                |
// | display_order           | int(10) unsigned | YES  |     | NULL    |                |
// | specialty_id            | int(10) unsigned | NO   | MUL | NULL    |                |

return array(
	'phrasebyspecialty1' => array(
		'name' => 'drug use',
		'phrase' => 'drug use',
		'section_by_specialty_id' => 10,
		'display_order' => 0,
		'specialty_id' => 8,
	),
	'phrasebyspecialty2' => array(
		'name' => 'alcoholism',
		'phrase' => 'alcoholism',
		'section_by_specialty_id' => 10,
		'display_order' => 0,
		'specialty_id' => 8,
	),
	'phrasebyspecialty3' => array(
		'name' => 'Loss of vision',
		'phrase' => 'Loss of vision',
		'section_by_specialty_id' => 1,
		'display_order' => 0,
		'specialty_id' => 8,
	),
	'phrasebyspecialty4' => array(
		'name' => 'Peripheral field loss',
		'phrase' => 'Peripheral field loss',
		'section_by_specialty_id' => 1,
		'display_order' => 1,
		'specialty_id' => 8,
	),
	'phrasebyspecialty5' => array(
		'name' => 'Distortion of vision',
		'phrase' => 'Distortion of vision',
		'section_by_specialty_id' => 1,
		'display_order' => 2,
		'specialty_id' => 8,
	),
	'phrasebyspecialty6' => array(
		'name' => 'Central vision disturbance',
		'phrase' => 'Central vision disturbance',
		'section_by_specialty_id' => 1,
		'display_order' => 3,
		'specialty_id' => 8,
	),
);
?>
