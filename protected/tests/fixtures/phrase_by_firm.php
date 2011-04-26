<?php
// | id			| int(10) unsigned | NO   | PRI | NULL	  | auto_increment |
// | name		| varchar(255)	   | YES  |	| NULL	  |		   |
// | phrase		| text		   | YES  |	| NULL	  |		   |
// | section_by_firm_id | int(10) unsigned | NO   | MUL | NULL	  |		   |
// | display_order	| int(10) unsigned | YES  |	| NULL	  |		   |
// | firm_id		| int(10) unsigned | NO   | MUL | NULL	  |		   |

return array(
	'address1' => array(
		'name' => 'Referral',
		'phrase' => 'Thanks for referring this [age] old [sub] who I saw today...',
		'section_by_firm_id' => 1,
		'display_order' => 1,
		'firm_id' => 1,
	),
	'address2' => array(
		'name' => 'Emergency',
		'phrase' => 'I saw this [age] old [sub] as an emergency',
		'section_by_firm_id' => 1,
		'display_order' => 2,
		'firm_id' => 1,
	),
	'address3' => array(
		'name' => '',
		'phrase' => '',
		'section_by_firm_id' => 2,
		'display_order' => 3,
		'firm_id' => 1,
	),
);
?>
