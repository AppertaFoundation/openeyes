<?php

class m210903_094449_extra_proc_benefits_risks extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'extra_procedure_complication',
            array(
                'id' => 'pk',
                'extra_proc_id' => 'int(11) NOT NULL',
                'complication_id' => 'int(10) unsigned NOT NULL',
            ),
            true
        );
        $this->addForeignKey('extra_procedure_complication_complication_id_fk', 'extra_procedure_complication', 'complication_id', 'complication', 'id');
        $this->addForeignKey('extra_procedure_complication_extra_proc_id_fk', 'extra_procedure_complication', 'extra_proc_id', 'ophtrconsent_procedure_extra', 'id');

        $this->createOETable(
            'extra_procedure_benefit',
            array(
                'id' => 'pk',
                'extra_proc_id' => 'int(11) NOT NULL',
                'benefit_id' => 'int(10) unsigned NOT NULL',
            ),
            true
        );
        $this->addForeignKey('extra_procedure_benefit_benefit_id_fk', 'extra_procedure_benefit', 'benefit_id', 'benefit', 'id');
        $this->addForeignKey('extra_procedure_benefit_extra_proc_id_fk', 'extra_procedure_benefit', 'extra_proc_id', 'ophtrconsent_procedure_extra', 'id');
    }

    public function down()
    {
        echo "m210903_094449_extra_proc_benefits_risks does not support migration down.\n";
        return false;
    }
}
