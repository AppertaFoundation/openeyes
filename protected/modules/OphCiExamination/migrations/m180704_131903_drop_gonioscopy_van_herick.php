<?php

class m180704_131903_drop_gonioscopy_van_herick extends CDbMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophciexamination_gonioscopy_right_van_herick_id_fk', 'et_ophciexamination_gonioscopy');
        $this->dropForeignKey('et_ophciexamination_gonioscopy_left_van_herick_id_fk', 'et_ophciexamination_gonioscopy');

        $this->dropColumn('et_ophciexamination_gonioscopy', 'left_van_herick_id');
        $this->dropColumn('et_ophciexamination_gonioscopy_version', 'left_van_herick_id');

        $this->dropColumn('et_ophciexamination_gonioscopy', 'right_van_herick_id');
        $this->dropColumn('et_ophciexamination_gonioscopy_version', 'right_van_herick_id');
    }

    public function down()
    {

        $this->addColumn('et_ophciexamination_gonioscopy', 'left_van_herick_id', 'int(10) unsigned DEFAULT NULL');
        $this->addColumn('et_ophciexamination_gonioscopy_version', 'left_van_herick_id', 'int(10) unsigned DEFAULT NULL');
        $this->addColumn('et_ophciexamination_gonioscopy', 'right_van_herick_id', 'int(10) unsigned DEFAULT NULL');
        $this->addColumn('et_ophciexamination_gonioscopy_version', 'right_van_herick_id', 'int(10) unsigned DEFAULT NULL');

        $this->addForeignKey('et_ophciexamination_gonioscopy_left_van_herick_id_fk', 'et_ophciexamination_gonioscopy', 'left_van_herick_id', 'ophciexamination_van_herick', 'id');
        $this->addForeignKey('et_ophciexamination_gonioscopy_right_van_herick_id_fk', 'et_ophciexamination_gonioscopy', 'right_van_herick_id', 'ophciexamination_van_herick', 'id');

        echo "m180704_131903_drop_gonioscopy_van_herick does not support migration down.\n";
        return false;
    }
}
