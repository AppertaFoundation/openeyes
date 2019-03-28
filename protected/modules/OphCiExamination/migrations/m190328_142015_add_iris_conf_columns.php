<?php

class m190328_142015_add_iris_conf_columns extends CDbMigration
{
  public function up()
  {
    $this->addColumn('et_ophciexamination_gonioscopy', 'right_iris_conf_id', 'INT(10)');
    $this->addColumn('et_ophciexamination_gonioscopy', 'left_iris_conf_id', 'INT(10)');
    $this->addColumn('et_ophciexamination_gonioscopy_version', 'right_iris_conf_id', 'INT(10)');
    $this->addColumn('et_ophciexamination_gonioscopy_version', 'left_iris_conf_id', 'INT(10)');

    $this->addForeignKey('et_ophciexamination_gonioscopy_right_iris_conf_id_fk',
      'et_ophciexamination_gonioscopy', 'right_iris_conf_id',
      'ophciexamination_gonioscopy_iris_configuration', 'id');
    $this->addForeignKey('et_ophciexamination_gonioscopy_left_iris_conf_id_fk',
      'et_ophciexamination_gonioscopy', 'left_iris_conf_id',
      'ophciexamination_gonioscopy_iris_configuration', 'id');
  }

  public function down()
  {
    $this->dropForeignKey('et_ophciexamination_gonioscopy_left_iris_conf_id_fk', 'et_ophciexamination_gonioscopy');
    $this->dropForeignKey('et_ophciexamination_gonioscopy_right_iris_conf_id_fk', 'et_ophciexamination_gonioscopy');

    $this->dropColumn('et_ophciexamination_gonioscopy_version', 'left_iris_conf_id');
    $this->dropColumn('et_ophciexamination_gonioscopy_version', 'right_iris_conf_id');
    $this->dropColumn('et_ophciexamination_gonioscopy', 'left_iris_conf_id');
    $this->dropColumn('et_ophciexamination_gonioscopy', 'right_iris_conf_id');
  }
}