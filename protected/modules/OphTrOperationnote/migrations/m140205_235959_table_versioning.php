<?php

class m140205_235959_table_versioning extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophtroperationnote_anaesthetic_anaesthetic_complications', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_buckle_drainage_type', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_cataract_complications', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_cataract_incision_site', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_cataract_incision_type', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_cataract_iol_position', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_cataract_iol_type', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_gas_type', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_gas_volume', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_gauge', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_preparation_intraocular_solution', 'active', 'boolean not null default true');
        $this->addColumn('ophtroperationnote_preparation_skin_preparation', 'active', 'boolean not null default true');

        $this->addColumn('ophtroperationnote_postop_drug', 'active', 'boolean not null default true');
        $this->update('ophtroperationnote_postop_drug', array('active' => new CDbExpression('not(deleted)')));
        $this->dropColumn('ophtroperationnote_postop_drug', 'deleted');

        $this->versionExistingTable('et_ophtroperationnote_anaesthetic');
        $this->versionExistingTable('et_ophtroperationnote_buckle');
        $this->versionExistingTable('et_ophtroperationnote_cataract');
        $this->versionExistingTable('et_ophtroperationnote_comments');
        $this->versionExistingTable('et_ophtroperationnote_genericprocedure');
        $this->versionExistingTable('et_ophtroperationnote_membrane_peel');
        $this->versionExistingTable('et_ophtroperationnote_personnel');
        $this->versionExistingTable('et_ophtroperationnote_postop_drugs');
        $this->versionExistingTable('et_ophtroperationnote_preparation');
        $this->versionExistingTable('et_ophtroperationnote_procedurelist');
        $this->versionExistingTable('et_ophtroperationnote_surgeon');
        $this->versionExistingTable('et_ophtroperationnote_tamponade');
        $this->versionExistingTable('et_ophtroperationnote_vitrectomy');
        $this->versionExistingTable('ophtroperationnote_anaesthetic_anaesthetic_agent');
        $this->versionExistingTable('ophtroperationnote_anaesthetic_anaesthetic_complication');
        $this->versionExistingTable('ophtroperationnote_anaesthetic_anaesthetic_complications');
        $this->versionExistingTable('ophtroperationnote_buckle_drainage_type');
        $this->versionExistingTable('ophtroperationnote_cataract_complication');
        $this->versionExistingTable('ophtroperationnote_cataract_complications');
        $this->versionExistingTable('ophtroperationnote_cataract_incision_site');
        $this->versionExistingTable('ophtroperationnote_cataract_incision_type');
        $this->versionExistingTable('ophtroperationnote_cataract_iol_position');
        $this->versionExistingTable('ophtroperationnote_cataract_iol_type');
        $this->versionExistingTable('ophtroperationnote_cataract_operative_device');
        $this->versionExistingTable('ophtroperationnote_gas_percentage');
        $this->versionExistingTable('ophtroperationnote_gas_type');
        $this->versionExistingTable('ophtroperationnote_gas_volume');
        $this->versionExistingTable('ophtroperationnote_gauge');
        $this->versionExistingTable('ophtroperationnote_postop_drug');
        $this->versionExistingTable('ophtroperationnote_postop_drugs_drug');
        $this->versionExistingTable('ophtroperationnote_postop_site_subspecialty_drug');
        $this->versionExistingTable('ophtroperationnote_preparation_intraocular_solution');
        $this->versionExistingTable('ophtroperationnote_preparation_skin_preparation');
        $this->versionExistingTable('ophtroperationnote_procedure_element');
        $this->versionExistingTable('ophtroperationnote_procedurelist_procedure_assignment');
        $this->versionExistingTable('ophtroperationnote_site_subspecialty_postop_instructions');
    }

    public function down()
    {
        $this->dropTable('et_ophtroperationnote_anaesthetic_version');
        $this->dropTable('et_ophtroperationnote_buckle_version');
        $this->dropTable('et_ophtroperationnote_cataract_version');
        $this->dropTable('et_ophtroperationnote_comments_version');
        $this->dropTable('et_ophtroperationnote_genericprocedure_version');
        $this->dropTable('et_ophtroperationnote_membrane_peel_version');
        $this->dropTable('et_ophtroperationnote_personnel_version');
        $this->dropTable('et_ophtroperationnote_postop_drugs_version');
        $this->dropTable('et_ophtroperationnote_preparation_version');
        $this->dropTable('et_ophtroperationnote_procedurelist_version');
        $this->dropTable('et_ophtroperationnote_surgeon_version');
        $this->dropTable('et_ophtroperationnote_tamponade_version');
        $this->dropTable('et_ophtroperationnote_vitrectomy_version');
        $this->dropTable('ophtroperationnote_anaesthetic_anaesthetic_agent_version');
        $this->dropTable('ophtroperationnote_anaesthetic_anaesthetic_complication_version');
        $this->dropTable('ophtroperationnote_anaesthetic_anaesthetic_complications_version');
        $this->dropTable('ophtroperationnote_buckle_drainage_type_version');
        $this->dropTable('ophtroperationnote_cataract_complication_version');
        $this->dropTable('ophtroperationnote_cataract_complications_version');
        $this->dropTable('ophtroperationnote_cataract_incision_site_version');
        $this->dropTable('ophtroperationnote_cataract_incision_type_version');
        $this->dropTable('ophtroperationnote_cataract_iol_position_version');
        $this->dropTable('ophtroperationnote_cataract_iol_type_version');
        $this->dropTable('ophtroperationnote_cataract_operative_device_version');
        $this->dropTable('ophtroperationnote_gas_percentage_version');
        $this->dropTable('ophtroperationnote_gas_type_version');
        $this->dropTable('ophtroperationnote_gas_volume_version');
        $this->dropTable('ophtroperationnote_gauge_version');
        $this->dropTable('ophtroperationnote_postop_drug_version');
        $this->dropTable('ophtroperationnote_postop_drugs_drug_version');
        $this->dropTable('ophtroperationnote_postop_site_subspecialty_drug_version');
        $this->dropTable('ophtroperationnote_preparation_intraocular_solution_version');
        $this->dropTable('ophtroperationnote_preparation_skin_preparation_version');
        $this->dropTable('ophtroperationnote_procedure_element_version');
        $this->dropTable('ophtroperationnote_procedurelist_procedure_assignment_version');
        $this->dropTable('ophtroperationnote_site_subspecialty_postop_instructions_version');

        $this->addColumn('ophtroperationnote_postop_drug', 'deleted', "tinyint(1) unsigned NOT NULL DEFAULT '0'");
        $this->update('ophtroperationnote_postop_drug', array('deleted' => new CDbExpression('not(active)')));
        $this->dropColumn('ophtroperationnote_postop_drug', 'active');

        $this->dropColumn('ophtroperationnote_anaesthetic_anaesthetic_complications', 'active');
        $this->dropColumn('ophtroperationnote_buckle_drainage_type', 'active');
        $this->dropColumn('ophtroperationnote_cataract_complications', 'active');
        $this->dropColumn('ophtroperationnote_cataract_incision_site', 'active');
        $this->dropColumn('ophtroperationnote_cataract_incision_type', 'active');
        $this->dropColumn('ophtroperationnote_cataract_iol_position', 'active');
        $this->dropColumn('ophtroperationnote_cataract_iol_type', 'active');
        $this->dropColumn('ophtroperationnote_gas_type', 'active');
        $this->dropColumn('ophtroperationnote_gas_volume', 'active');
        $this->dropColumn('ophtroperationnote_gauge', 'active');
        $this->dropColumn('ophtroperationnote_preparation_intraocular_solution', 'active');
        $this->dropColumn('ophtroperationnote_preparation_skin_preparation', 'active');
    }
}
