<?php

class m181022_082527_add_is_multiselect_column_to_ophciexamination_attribute extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('ophciexamination_attribute' , 'is_multiselect' , 'tinyint(1) not null default 1');
	    $this->addColumn('ophciexamination_attribute_version' , 'is_multiselect' , 'tinyint(1) not null default 1');
	}

	public function down()
	{
		$this->dropColumn('ophciexamination_attribute' , 'is_multiselect');
		$this->dropColumn('ophciexamination_attribute_version' , 'is_multiselect');
	}
}