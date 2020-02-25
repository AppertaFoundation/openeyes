<?php

class m191213_182917_extend_mview_datapoint_node_json_content extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('mview_datapoint_node', 'content_json', 'TEXT');
	}

	public function down()
	{
        $this->alterColumn('mview_datapoint_node', 'content_json', 'VARCHAR(4000) NOT NULL');
	}
}
