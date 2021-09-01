<?php

class m210801_044200_consent_form_template extends OEMigration
{
    public function up()
    {
        $this->createOETable('ophtrconsent_template', array(
            'id'                => 'pk',
            'name'              => 'VARCHAR(255) NULL',
            'institution_id'    => 'int(10) unsigned DEFAULT NULL',
            'site_id'           => 'int(10) unsigned DEFAULT NULL',
            'subspecialty_id'   => 'int(10) unsigned DEFAULT NULL',
            'type_id'           => 'int(10) unsigned NOT NULL'
        ), true);

        $this->addForeignKey(
            'ophtrconsent_template_institution_fk',
            'ophtrconsent_template',
            'institution_id',
            'institution',
            'id'
        );

        $this->addForeignKey(
            'ophtrconsent_template_site_fk',
            'ophtrconsent_template',
            'site_id',
            'site',
            'id'
        );

        $this->addForeignKey(
            'ophtrconsent_template_subspecialty_fk',
            'ophtrconsent_template',
            'subspecialty_id',
            'subspecialty',
            'id'
        );

        $this->addForeignKey(
            'ophtrconsent_template_type_fk',
            'ophtrconsent_template',
            'type_id',
            'ophtrconsent_type_type',
            'id'
        );

        $this->createOETable('ophtrconsent_template_procedure', array(
            'id'                => 'pk',
            'procedure_id'      => 'int(10) unsigned NOT NULL',
            'template_id'          => 'int(11) NOT NULL',
        ), true);

        $this->addForeignKey(
            'ophtrconsent_template_procedure_p_fk',
            'ophtrconsent_template_procedure',
            'procedure_id',
            'proc',
            'id'
        );

        $this->addForeignKey(
            'ophtrconsent_template_procedure_t_fk',
            'ophtrconsent_template_procedure',
            'template_id',
            'ophtrconsent_template',
            'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('ophtrconsent_template_procedure_t_fk', 'ophtrconsent_template_procedure');
        $this->dropForeignKey('ophtrconsent_template_procedure_p_fk', 'ophtrconsent_template_procedure');
        $this->dropOETable('ophtrconsent_template_procedure', true);
        $this->dropForeignKey('ophtrconsent_template_type_fk', 'ophtrconsent_template');
        $this->dropForeignKey('ophtrconsent_template_subspecialty_fk', 'ophtrconsent_template');
        $this->dropForeignKey('ophtrconsent_template_site_fk', 'ophtrconsent_template');
        $this->dropForeignKey('ophtrconsent_template_institution_fk', 'ophtrconsent_template');
        $this->dropOETable('ophtrconsent_template', true);
    }
}
