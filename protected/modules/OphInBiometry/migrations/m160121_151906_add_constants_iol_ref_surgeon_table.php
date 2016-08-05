<?php

class m160121_151906_add_constants_iol_ref_surgeon_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('ophinbiometry_surgeon', array(
            'id' => 'pk',
            'name' => 'varchar(255) NOT NULL', ));

        $this->addColumn('et_ophinbiometry_iol_ref_values', 'constant', 'decimal(7,3)');
        $this->addColumn('et_ophinbiometry_iol_ref_values', 'constant1', 'decimal(7,3)');
        $this->addColumn('et_ophinbiometry_iol_ref_values', 'constant2', 'decimal(7,3)');
        $this->addColumn('et_ophinbiometry_iol_ref_values', 'active', 'boolean default true');
        $this->addColumn('et_ophinbiometry_iol_ref_values', 'surgeon_id', 'int(11)');
        $this->addForeignKey('surgeon_fk', 'et_ophinbiometry_iol_ref_values', 'surgeon_id', 'ophinbiometry_surgeon', 'id');

        $this->addColumn('et_ophinbiometry_iol_ref_values_version', 'constant', 'decimal(7,3)');
        $this->addColumn('et_ophinbiometry_iol_ref_values_version', 'constant1', 'decimal(7,3)');
        $this->addColumn('et_ophinbiometry_iol_ref_values_version', 'constant2', 'decimal(7,3)');
        $this->addColumn('et_ophinbiometry_iol_ref_values_version', 'active', 'boolean default true');
        $this->addColumn('et_ophinbiometry_iol_ref_values_version', 'surgeon_id', 'int(11)');
    }

    public function down()
    {
        $this->dropColumn('et_ophinbiometry_iol_ref_values', 'constant');
        $this->dropColumn('et_ophinbiometry_iol_ref_values', 'constant1');
        $this->dropColumn('et_ophinbiometry_iol_ref_values', 'constant2');
        $this->dropColumn('et_ophinbiometry_iol_ref_values', 'active');
        $this->dropForeignKey('surgeon_fk', 'et_ophinbiometry_iol_ref_values');
        $this->dropColumn('et_ophinbiometry_iol_ref_values', 'surgeon_id');

        $this->dropColumn('et_ophinbiometry_iol_ref_values_version', 'constant');
        $this->dropColumn('et_ophinbiometry_iol_ref_values_version', 'constant1');
        $this->dropColumn('et_ophinbiometry_iol_ref_values_version', 'constant2');
        $this->dropColumn('et_ophinbiometry_iol_ref_values_version', 'active');
        $this->dropColumn('et_ophinbiometry_iol_ref_values_version', 'surgeon_id');

        $this->dropTable('ophinbiometry_surgeon');
    }
}
