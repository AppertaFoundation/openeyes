<?php

class m141217_134213_pdf_footer_tag extends OEMigration
{
	public function up()
	{
		$this->createOETable(
			'pdf_footer_tag',
			array(
				'id' => 'pk',
				'event_type_id' => 'integer unsigned not null',
				'tag_name' => 'string not null',
				'method' => 'string not null',
				'constraint pdf_footer_tag_event_type_id_fk foreign key (event_type_id) references event_type (id)',
			),
			true
		);
	}

	public function down()
	{
		$this->dropTable('pdf_footer_tag_version');
		$this->dropTable('pdf_footer_tag');
	}
}
