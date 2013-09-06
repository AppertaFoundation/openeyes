<?php

class m130906_162100_anaesthetic_type_sort extends OEMigration
{
	public function up()
	{
		$this->addColumn('anaesthetic_type', 'display_order', 'int(10)');
		$migrations_path = dirname(__FILE__);
		$this->initialiseData($migrations_path, 'code');
	}

	public function down()
	{
		$this->dropColumn('anaesthetic_type', 'display_order');
	}
}
