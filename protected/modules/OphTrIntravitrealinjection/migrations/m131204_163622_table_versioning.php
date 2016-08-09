<?php

class m131204_163622_table_versioning extends OEMigration
{
    public function up()
    {
        $this->addColumn('ophtrintravitinjection_antiseptic_drug', 'active', 'boolean not null default true');
        $this->addColumn('ophtrintravitinjection_complicat', 'active', 'boolean not null default true');
        $this->addColumn('ophtrintravitinjection_ioplowering', 'active', 'boolean not null default true');
        $this->addColumn('ophtrintravitinjection_lens_status', 'active', 'boolean not null default true');
        $this->addColumn('ophtrintravitinjection_postinjection_drops', 'active', 'boolean not null default true');
        $this->addColumn('ophtrintravitinjection_skin_drug', 'active', 'boolean not null default true');

        $this->renameColumn('ophtrintravitinjection_treatment_drug', 'available', 'active');

        $this->versionExistingTable('et_ophtrintravitinjection_anaesthetic');
        $this->versionExistingTable('et_ophtrintravitinjection_anteriorseg');
        $this->versionExistingTable('et_ophtrintravitinjection_complications');
        $this->versionExistingTable('et_ophtrintravitinjection_postinject');
        $this->versionExistingTable('et_ophtrintravitinjection_site');
        $this->versionExistingTable('et_ophtrintravitinjection_treatment');
        $this->versionExistingTable('ophtrintravitinjection_anaestheticagent');
        $this->versionExistingTable('ophtrintravitinjection_anaestheticdelivery');
        $this->versionExistingTable('ophtrintravitinjection_anaesthetictype');
        $this->versionExistingTable('ophtrintravitinjection_antiseptic_allergy_assignment');
        $this->versionExistingTable('ophtrintravitinjection_antiseptic_drug');
        $this->versionExistingTable('ophtrintravitinjection_complicat');
        $this->versionExistingTable('ophtrintravitinjection_complicat_assignment');
        $this->versionExistingTable('ophtrintravitinjection_injectionuser');
        $this->versionExistingTable('ophtrintravitinjection_ioplowering');
        $this->versionExistingTable('ophtrintravitinjection_ioplowering_assign');
        $this->versionExistingTable('ophtrintravitinjection_lens_status');
        $this->versionExistingTable('ophtrintravitinjection_postinjection_drops');
        $this->versionExistingTable('ophtrintravitinjection_skin_drug');
        $this->versionExistingTable('ophtrintravitinjection_skindrug_allergy_assignment');
        $this->versionExistingTable('ophtrintravitinjection_treatment_drug');
    }

    public function down()
    {
        $this->dropTable('et_ophtrintravitinjection_anaesthetic_version');
        $this->dropTable('et_ophtrintravitinjection_anteriorseg_version');
        $this->dropTable('et_ophtrintravitinjection_complications_version');
        $this->dropTable('et_ophtrintravitinjection_postinject_version');
        $this->dropTable('et_ophtrintravitinjection_site_version');
        $this->dropTable('et_ophtrintravitinjection_treatment_version');
        $this->dropTable('ophtrintravitinjection_anaestheticagent_version');
        $this->dropTable('ophtrintravitinjection_anaestheticdelivery_version');
        $this->dropTable('ophtrintravitinjection_anaesthetictype_version');
        $this->dropTable('ophtrintravitinjection_antiseptic_allergy_assignment_version');
        $this->dropTable('ophtrintravitinjection_antiseptic_drug_version');
        $this->dropTable('ophtrintravitinjection_complicat_version');
        $this->dropTable('ophtrintravitinjection_complicat_assignment_version');
        $this->dropTable('ophtrintravitinjection_injectionuser_version');
        $this->dropTable('ophtrintravitinjection_ioplowering_version');
        $this->dropTable('ophtrintravitinjection_ioplowering_assign_version');
        $this->dropTable('ophtrintravitinjection_lens_status_version');
        $this->dropTable('ophtrintravitinjection_postinjection_drops_version');
        $this->dropTable('ophtrintravitinjection_skin_drug_version');
        $this->dropTable('ophtrintravitinjection_skindrug_allergy_assignment_version');
        $this->dropTable('ophtrintravitinjection_treatment_drug_version');

        $this->renameColumn('ophtrintravitinjection_treatment_drug', 'active', 'available');

        $this->dropColumn('ophtrintravitinjection_antiseptic_drug', 'active');
        $this->dropColumn('ophtrintravitinjection_complicat', 'active');
        $this->dropColumn('ophtrintravitinjection_ioplowering', 'active');
        $this->dropColumn('ophtrintravitinjection_lens_status', 'active');
        $this->dropColumn('ophtrintravitinjection_postinjection_drops', 'active');
        $this->dropColumn('ophtrintravitinjection_skin_drug', 'active');
    }
}
