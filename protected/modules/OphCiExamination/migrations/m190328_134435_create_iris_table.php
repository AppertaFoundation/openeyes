<?php

class m190328_134435_create_iris_table extends OEMigration
{
	public function up()
	{
    $this->createOETable('ophciexamination_gonioscopy_iris', [
        'id' => 'pk',
        'name' => 'varchar(50) NOT NULL',
        'display_order' => 'tinyint(3) unsigned DEFAULT \'0\'',
    ], true);

    $this->insertMultiple('ophciexamination_gonioscopy_iris', [
        ['name' => 'Flat', 'display_order' => '1',],
        ['name' => 'Plateau', 'display_order' => '2',],
        ['name' => 'Concave', 'display_order' => '3',],
        ['name' => 'Bombé', 'display_order' => '4',],
    ]);

    $this->addColumn('et_ophciexamination_gonioscopy', 'right_iris_id', 'INT(10) DEFAULT NULL');
    $this->addColumn('et_ophciexamination_gonioscopy', 'left_iris_id', 'INT(10) DEFAULT NULL');
    $this->addColumn('et_ophciexamination_gonioscopy_version', 'right_iris_id', 'INT(10) DEFAULT NULL');
    $this->addColumn('et_ophciexamination_gonioscopy_version', 'left_iris_id', 'INT(10) DEFAULT NULL');

    $this->addForeignKey('et_ophciexamination_gonioscopy_right_iris_id_fk',
      'et_ophciexamination_gonioscopy', 'right_iris_id',
      'ophciexamination_gonioscopy_iris', 'id');
    $this->addForeignKey('et_ophciexamination_gonioscopy_left_iris_id_fk',
      'et_ophciexamination_gonioscopy', 'left_iris_id',
      'ophciexamination_gonioscopy_iris', 'id');
	}

	public function down()
	{
    $this->dropForeignKey('et_ophciexamination_gonioscopy_left_iris_id_fk', 'et_ophciexamination_gonioscopy');
    $this->dropForeignKey('et_ophciexamination_gonioscopy_right_iris_id_fk', 'et_ophciexamination_gonioscopy');

    $this->dropColumn('et_ophciexamination_gonioscopy_version', 'left_iris_id');
    $this->dropColumn('et_ophciexamination_gonioscopy_version', 'right_iris_id');
    $this->dropColumn('et_ophciexamination_gonioscopy', 'left_iris_id');
    $this->dropColumn('et_ophciexamination_gonioscopy', 'right_iris_id');

    $this->dropOETable('ophciexamination_gonioscopy_iris', true);
	}
}