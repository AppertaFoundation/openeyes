<?php
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
?>
