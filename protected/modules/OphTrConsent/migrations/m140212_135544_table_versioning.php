<?php

class m140212_135544_table_versioning extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophtrconsent_type_type', 'active', 'boolean not null default true');
        $this->addColumn('ophtrconsent_leaflet', 'active', 'boolean not null default true');

        $this->versionExistingTable('et_ophtrconsent_benfitrisk');
        $this->versionExistingTable('et_ophtrconsent_leaflets');
        $this->versionExistingTable('et_ophtrconsent_other');
        $this->versionExistingTable('et_ophtrconsent_permissions');
        $this->versionExistingTable('et_ophtrconsent_procedure');
        $this->versionExistingTable('et_ophtrconsent_type');
        $this->versionExistingTable('ophtrconsent_type_type');
        $this->versionExistingTable('ophtrconsent_leaflet');
        $this->versionExistingTable('ophtrconsent_leaflet_firm');
        $this->versionExistingTable('ophtrconsent_leaflet_subspecialty');
        $this->versionExistingTable('ophtrconsent_leaflets');
        $this->versionExistingTable('ophtrconsent_permissions_images');
        $this->versionExistingTable('ophtrconsent_procedure_add_procs_add_procs');
        $this->versionExistingTable('ophtrconsent_procedure_proc_defaults');
        $this->versionExistingTable('ophtrconsent_procedure_procedures_procedures');
    }

    public function down()
    {
        $this->dropTable('et_ophtrconsent_benfitrisk_version');
        $this->dropTable('et_ophtrconsent_leaflets_version');
        $this->dropTable('et_ophtrconsent_other_version');
        $this->dropTable('et_ophtrconsent_permissions_version');
        $this->dropTable('ophtrconsent_permissions_images_version');
        $this->dropTable('et_ophtrconsent_procedure_version');
        $this->dropTable('ophtrconsent_procedure_add_procs_add_procs_version');
        $this->dropTable('ophtrconsent_procedure_proc_defaults_version');
        $this->dropTable('ophtrconsent_procedure_procedures_procedures_version');
        $this->dropTable('et_ophtrconsent_type_version');
        $this->dropTable('ophtrconsent_type_type_version');
        $this->dropTable('ophtrconsent_leaflet_version');
        $this->dropTable('ophtrconsent_leaflet_firm_version');
        $this->dropTable('ophtrconsent_leaflet_subspecialty_version');
        $this->dropTable('ophtrconsent_leaflets_version');

        $this->dropColumn('ophtrconsent_leaflet', 'active');
        $this->dropColumn('ophtrconsent_type_type', 'active');
    }
}
