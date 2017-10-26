<?php

class m171026_084312_add_FKs_to_examination_cxl_tables extends OEMigration
{
	public function up()
	{
	    // et_ophciexamination_cxl_history
        $this->addForeignKey('et_ophciexamination_cxl_history_event', 'et_ophciexamination_cxl_history', 'event_id', 'event', 'id');
        $this->alterColumn('et_ophciexamination_cxl_history', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_history_version', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->addForeignKey('et_ophciexamination_cxl_history_eye', 'et_ophciexamination_cxl_history', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophciexamination_cxl_history_ocular_surface_disease', 'et_ophciexamination_cxl_history', 'ocular_surface_disease_id', 'ophciexamination_cxl_ocular_surface_disease', 'id');

        //et_ophciexamination_cxl_outcome
        $this->addForeignKey('et_ophciexamination_cxl_outcome_event', 'et_ophciexamination_cxl_outcome', 'event_id', 'event', 'id');
        $this->alterColumn('et_ophciexamination_cxl_outcome', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_outcome_version', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->addForeignKey('et_ophciexamination_cxl_outcomey_eye', 'et_ophciexamination_cxl_outcome', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophciexamination_cxl_outcomey_diagnosis', 'et_ophciexamination_cxl_outcome', 'diagnosis_id', 'ophciexamination_cxl_outcome_diagnosis', 'id');
        $this->addForeignKey('et_ophciexamination_cxl_outcomey_outcome', 'et_ophciexamination_cxl_outcome', 'outcome_id', 'ophciexamination_cxl_outcome', 'id');

        //et_ophciexamination_keratometry
        $this->addForeignKey('et_ophciexamination_keratometry_event', 'et_ophciexamination_keratometry', 'event_id', 'event', 'id');
        $this->alterColumn('et_ophciexamination_keratometry', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_keratometry_version', 'eye_id', 'int(10) UNSIGNED DEFAULT 3');
        $this->addForeignKey('et_ophciexamination_keratometry_eye', 'et_ophciexamination_keratometry', 'eye_id', 'eye', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_tomographer', 'et_ophciexamination_keratometry', 'tomographer_id', 'ophciexamination_tomographer_device', 'id');

        $this->dropColumn('et_ophciexamination_keratometry','tomographer_scan_quality_id');

        $this->addForeignKey('et_ophciexamination_keratometry_rgf', 'et_ophciexamination_keratometry', 'right_quality_front', 'ophciexamination_cxl_quality_score', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_rgb', 'et_ophciexamination_keratometry', 'right_quality_back', 'ophciexamination_cxl_quality_score', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_lgf', 'et_ophciexamination_keratometry', 'left_quality_front', 'ophciexamination_cxl_quality_score', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_lgb', 'et_ophciexamination_keratometry', 'left_quality_back', 'ophciexamination_cxl_quality_score', 'id');

        $this->addForeignKey('et_ophciexamination_keratometry_rclr', 'et_ophciexamination_keratometry', 'right_cl_removed', 'ophciexamination_cxl_cl_removed', 'id');
        $this->addForeignKey('et_ophciexamination_keratometry_lclr', 'et_ophciexamination_keratometry', 'left_cl_removed', 'ophciexamination_cxl_cl_removed', 'id');

	}

	public function down()
	{
        // et_ophciexamination_cxl_history
		$this->dropForeignKey('et_ophciexamination_cxl_history_event', 'et_ophciexamination_cxl_history');
		$this->dropForeignKey('et_ophciexamination_cxl_history_eye', 'et_ophciexamination_cxl_history');
        $this->alterColumn('et_ophciexamination_cxl_history', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_history_version', 'eye_id', 'int(11) SIGNED DEFAULT 3');
		$this->dropForeignKey('et_ophciexamination_cxl_history_ocular_surface_disease', 'et_ophciexamination_cxl_history');

        //et_ophciexamination_cxl_outcome
        $this->dropForeignKey('et_ophciexamination_cxl_outcome_event', 'et_ophciexamination_cxl_outcome');
        $this->dropForeignKey('et_ophciexamination_cxl_outcomey_eye', 'et_ophciexamination_cxl_outcome');
        $this->alterColumn('et_ophciexamination_cxl_outcome', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_cxl_outcome_version', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->dropForeignKey('et_ophciexamination_cxl_outcomey_diagnosis', 'et_ophciexamination_cxl_outcome');
        $this->dropForeignKey('et_ophciexamination_cxl_outcomey_outcome', 'et_ophciexamination_cxl_outcome');

        //et_ophciexamination_keratometry
        $this->dropForeignKey('et_ophciexamination_keratometry_event', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_eye', 'et_ophciexamination_keratometry');
        $this->alterColumn('et_ophciexamination_keratometry', 'eye_id', 'int(11) SIGNED DEFAULT 3');
        $this->alterColumn('et_ophciexamination_keratometry_version', 'eye_id', 'int(11) SIGNED DEFAULT 3');

        $this->dropForeignKey('et_ophciexamination_keratometry_tomographer', 'et_ophciexamination_keratometry');

        $this->addColumn('et_ophciexamination_keratometry', 'tomographer_scan_quality_id', 'INT(10)');

        $this->dropForeignKey('et_ophciexamination_keratometry_rgf', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_rgb', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_lgf', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_lgb', 'et_ophciexamination_keratometry');

        $this->dropForeignKey('et_ophciexamination_keratometry_rclr', 'et_ophciexamination_keratometry');
        $this->dropForeignKey('et_ophciexamination_keratometry_lclr', 'et_ophciexamination_keratometry');
	}

}