<?php

class m180412_153258_tbl_secondary_diagnosis_not_present extends OEMigration
{
    private static $_tbl = 'secondary_diagnosis_not_present';

    public function up()
    {
        $this->createOETable(self::$_tbl, array(
            'id' => 'pk',
            'disorder_id' => 'bigint(20) unsigned',
            'eye_id' => 'int(10) unsigned',
            'patient_id' => 'int(10) unsigned',
            'date' => 'varchar(10)'
        ), true);

        $this->addForeignKey('fk_secondary_diag_np_disorder_id', self::$_tbl, 'disorder_id', 'disorder', 'id');
        $this->addForeignKey('fk_secondary_diag_np_eye_id', self::$_tbl, 'eye_id', 'eye', 'id');
        $this->addForeignKey('fk_secondary_diag_np_patient_id', self::$_tbl, 'patient_id', 'patient', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_secondary_diag_np_disorder_id', self::$_tbl);
        $this->dropForeignKey('fk_secondary_diag_np_eye_id', self::$_tbl);
        $this->dropForeignKey('fk_secondary_diag_np_patient_id', self::$_tbl);
        $this->dropOETable(self::$_tbl, true);
    }

}
