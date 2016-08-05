<?php

class m140212_130459_table_versioning extends OEMigration
{
    public function up()
    {
        $this->renameColumn('ophcotherapya_exceptional_deviationreason', 'enabled', 'active');
        $this->renameColumn('ophcotherapya_exceptional_standardintervention', 'enabled', 'active');
        $this->renameColumn('ophcotherapya_exceptional_startperiod', 'enabled', 'active');

        $this->addColumn('ophcotherapya_treatment', 'active', 'boolean not null default true');
        $this->addColumn('ophcotherapya_filecoll', 'active', 'boolean not null default true');
        $this->addColumn('ophcotherapya_relevanttreatment', 'active', 'boolean not null default true');

        $this->versionExistingTable('et_ophcotherapya_exceptional');
        $this->versionExistingTable('ophcotherapya_exceptional_intervention');
        $this->versionExistingTable('et_ophcotherapya_mrservicein');
        $this->versionExistingTable('et_ophcotherapya_patientsuit');
        $this->versionExistingTable('et_ophcotherapya_relativecon');
        $this->versionExistingTable('et_ophcotherapya_therapydiag');
        $this->versionExistingTable('ophcotherapya_decisiontree');
        $this->versionExistingTable('ophcotherapya_decisiontreenode');
        $this->versionExistingTable('ophcotherapya_decisiontreenode_responsetype');
        $this->versionExistingTable('ophcotherapya_decisiontreenodechoice');
        $this->versionExistingTable('ophcotherapya_decisiontreenoderule');
        $this->versionExistingTable('ophcotherapya_decisiontreeoutcome');
        $this->versionExistingTable('ophcotherapya_email');
        $this->versionExistingTable('ophcotherapya_email_attachment');
        $this->versionExistingTable('ophcotherapya_exceptional_deviationreason');
        $this->versionExistingTable('ophcotherapya_exceptional_deviationreason_ass');
        $this->versionExistingTable('ophcotherapya_exceptional_filecoll_assignment');
        $this->versionExistingTable('ophcotherapya_exceptional_pastintervention');
        $this->versionExistingTable('ophcotherapya_exceptional_pastintervention_stopreason');
        $this->versionExistingTable('ophcotherapya_exceptional_standardintervention');
        $this->versionExistingTable('ophcotherapya_exceptional_startperiod');
        $this->versionExistingTable('ophcotherapya_filecoll');
        $this->versionExistingTable('ophcotherapya_filecoll_assignment');
        $this->versionExistingTable('ophcotherapya_patientsuit_decisiontreenoderesponse');
        $this->versionExistingTable('ophcotherapya_relevanttreatment');
        $this->versionExistingTable('ophcotherapya_therapydisorder');
        $this->versionExistingTable('ophcotherapya_treatment');
        $this->versionExistingTable('ophcotherapya_treatment_cost_type');
        $this->versionExistingTable('ophcotherapya_email_recipient');
        $this->versionExistingTable('ophcotherapya_email_recipient_type');
    }

    public function down()
    {
        $this->dropTable('ophcotherapya_email_recipient_type_version');
        $this->dropTable('ophcotherapya_email_recipient_version');
        $this->dropTable('et_ophcotherapya_exceptional_version');
        $this->dropTable('ophcotherapya_exceptional_intervention_version');
        $this->dropTable('et_ophcotherapya_mrservicein_version');
        $this->dropTable('et_ophcotherapya_patientsuit_version');
        $this->dropTable('et_ophcotherapya_relativecon_version');
        $this->dropTable('et_ophcotherapya_therapydiag_version');
        $this->dropTable('ophcotherapya_decisiontree_version');
        $this->dropTable('ophcotherapya_decisiontreenode_version');
        $this->dropTable('ophcotherapya_decisiontreenode_responsetype_version');
        $this->dropTable('ophcotherapya_decisiontreenodechoice_version');
        $this->dropTable('ophcotherapya_decisiontreenoderule_version');
        $this->dropTable('ophcotherapya_decisiontreeoutcome_version');
        $this->dropTable('ophcotherapya_email_version');
        $this->dropTable('ophcotherapya_email_attachment_version');
        $this->dropTable('ophcotherapya_exceptional_deviationreason_version');
        $this->dropTable('ophcotherapya_exceptional_deviationreason_ass_version');
        $this->dropTable('ophcotherapya_exceptional_filecoll_assignment_version');
        $this->dropTable('ophcotherapya_exceptional_pastintervention_version');
        $this->dropTable('ophcotherapya_exceptional_pastintervention_stopreason_version');
        $this->dropTable('ophcotherapya_exceptional_standardintervention_version');
        $this->dropTable('ophcotherapya_exceptional_startperiod_version');
        $this->dropTable('ophcotherapya_filecoll_version');
        $this->dropTable('ophcotherapya_filecoll_assignment_version');
        $this->dropTable('ophcotherapya_patientsuit_decisiontreenoderesponse_version');
        $this->dropTable('ophcotherapya_relevanttreatment_version');
        $this->dropTable('ophcotherapya_therapydisorder_version');
        $this->dropTable('ophcotherapya_treatment_version');
        $this->dropTable('ophcotherapya_treatment_cost_type_version');

        $this->dropColumn('ophcotherapya_filecoll', 'active');
        $this->dropColumn('ophcotherapya_relevanttreatment', 'active');
        $this->dropColumn('ophcotherapya_treatment', 'active');

        $this->renameColumn('ophcotherapya_exceptional_deviationreason', 'active', 'enabled');
        $this->renameColumn('ophcotherapya_exceptional_standardintervention', 'active', 'enabled');
        $this->renameColumn('ophcotherapya_exceptional_startperiod', 'active', 'enabled');
    }
}
