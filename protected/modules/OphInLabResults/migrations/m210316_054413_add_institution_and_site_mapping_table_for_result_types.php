<?php

class m210316_054413_add_institution_and_site_mapping_table_for_result_types extends OEMigration
{
    public function up()
    {
        $this->createOETable(
            'ophinlabresults_type_institution',
            array(
                'id' => 'pk',
                'labresults_type_id' => 'int(11) NOT NULL',
                'institution_id' => 'int(10) unsigned NOT NULL',
            ),
            true
        );

        $this->addForeignKey(
            'ophinlabresults_type_institution_i_fk',
            'ophinlabresults_type_institution',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'ophinlabresults_type_institution_t_fk',
            'ophinlabresults_type_institution',
            'labresults_type_id',
            'ophinlabresults_type',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey(
            'ophinlabresults_type_institution_i_fk',
            'ophinlabresults_type_institution',
        );

        $this->dropForeignKey(
            'ophinlabresults_type_institution_t_fk',
            'ophinlabresults_type_institution',
        );

        $this->dropOETable(
            'ophinlabresults_type_institution',
            true
        );
    }
}
