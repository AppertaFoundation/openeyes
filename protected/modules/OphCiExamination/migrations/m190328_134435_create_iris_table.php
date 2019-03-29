<?php

class m190328_134435_create_iris_table extends OEMigration
{
	public function up()
	{
    $this->createOETable('ophciexamination_gonioscopy_iris',
      [
      'id' => 'pk',
      'name' => 'varchar(50) NOT NULL',
      'display_order' => 'tinyint(3) unsigned DEFAULT \'0\'',
      ],
      true);

    $this->insert('ophciexamination_gonioscopy_iris',
      ['name' => 'Flat', 'display_order' => '1',]);
    $this->insert('ophciexamination_gonioscopy_iris',
      ['name' => 'Plateau', 'display_order' => '2',]);
    $this->insert('ophciexamination_gonioscopy_iris',
      ['name' => 'Concave', 'display_order' => '3',]);
    $this->insert('ophciexamination_gonioscopy_iris',
      ['name' => 'Bombé', 'display_order' => '4',]);
	}

	public function down()
	{
    $this->dropOETable('ophciexamination_gonioscopy_iris', true);
	}
}