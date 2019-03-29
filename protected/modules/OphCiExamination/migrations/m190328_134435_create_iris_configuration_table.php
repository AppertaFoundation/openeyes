<?php

class m190328_134435_create_iris_configuration_table extends OEMigration
{
	public function up()
	{
    $this->createOETable('ophciexamination_gonioscopy_iris_configuration',
      [
      'id' => 'pk',
      'name' => 'varchar(20) NOT NULL',
      'display_order' => 'tinyint(3) unsigned DEFAULT \'0\'',
      ],
      true);

    $this->insert('ophciexamination_gonioscopy_iris_configuration',
      ['name' => 'Flat', 'display_order' => '1',]);
    $this->insert('ophciexamination_gonioscopy_iris_configuration',
      ['name' => 'Plateau', 'display_order' => '2',]);
    $this->insert('ophciexamination_gonioscopy_iris_configuration',
      ['name' => 'Concave', 'display_order' => '3',]);
    $this->insert('ophciexamination_gonioscopy_iris_configuration',
      ['name' => 'Bombé', 'display_order' => '4',]);
	}

	public function down()
	{
    $this->dropOETable('ophciexamination_gonioscopy_iris_configuration', true);
	}
}