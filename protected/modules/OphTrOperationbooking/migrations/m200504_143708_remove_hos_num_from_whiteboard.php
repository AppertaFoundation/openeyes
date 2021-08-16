<?php

class m200504_143708_remove_hos_num_from_whiteboard extends OEMigration
{

	public function safeUp()
	{
	    $this->dropOEColumn('ophtroperationbooking_whiteboard', 'hos_num', true);
	}

	public function safeDown()
	{
	    $this->addOEColumn('ophtroperationbooking_whiteboard', 'hos_num', 'varchar(40)', true);
	}
}