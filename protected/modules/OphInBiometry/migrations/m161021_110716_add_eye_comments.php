<?php

class m161021_110716_add_eye_comments extends CDbMigration
{
	public function up()
	{
        $this->addColumn('comments_left', 'et_ophinbiometry_calculation', 'text');
        $this->addColumn('comments_right', 'et_ophinbiometry_calculation', 'text');
	}

	public function down()
	{
        $this->dropColumn('comments_left', 'et_ophinbiometry_calculation');
        $this->dropColumn('comments_right', 'et_ophinbiometry_calculation');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}