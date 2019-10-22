<?php

class m191022_024419_create_procedure_risk_table extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'procedure_risk',
            array(
                'id' => 'pk',
                'proc_id' => 'int(10) unsigned NOT NULL',
                'risk_id' => 'int(11) NOT NULL',
                'CONSTRAINT `procedure_risk_proc_id_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
                'CONSTRAINT `procedure_risk_r_id_fk` FOREIGN KEY (`risk_id`) REFERENCES `ophciexamination_risk` (`id`)'
            ),
            true
        );
    }

    public function down()
    {
        $this->dropForeignKey(
            'procedure_risk_proc_id_fk',
            'procedure_risk'
        );
        $this->dropForeignKey(
            'procedure_risk_r_id_fk',
            'procedure_risk'
        );

        $this->dropOETable(
            'procedure_risk',
            true
        );
    }
}