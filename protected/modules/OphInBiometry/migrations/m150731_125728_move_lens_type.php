<?php

class m150731_125728_move_lens_type extends CDbMigration
{
    public function up()
    {
        //Drop key
        $this->dropForeignKey('ophinbiometry_lenstype_lens_l_fk', 'et_ophinbiometry_lenstype');
        $this->dropForeignKey('ophinbiometry_lenstype_lens_r_fk', 'et_ophinbiometry_lenstype');

        //Drop and add on main table
        $this->dropColumn('et_ophinbiometry_lenstype', 'lens_id_left');
        $this->dropColumn('et_ophinbiometry_lenstype', 'lens_id_right');
        $this->addColumn('et_ophinbiometry_selection', 'lens_id_left', 'int(10) unsigned NOT NULL DEFAULT 1');
        $this->addColumn('et_ophinbiometry_selection', 'lens_id_right', 'int(10) unsigned NOT NULL DEFAULT 1');

        //Drop and add on version tables
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'lens_id_left');
        $this->dropColumn('et_ophinbiometry_lenstype_version', 'lens_id_right');
        $this->addColumn('et_ophinbiometry_selection_version', 'lens_id_left', 'int(10) unsigned NOT NULL DEFAULT 1');
        $this->addColumn('et_ophinbiometry_selection_version', 'lens_id_right', 'int(10) unsigned NOT NULL DEFAULT 1');

        //Add new key
        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_l_fk',
            'et_ophinbiometry_selection',
            'lens_id_left',
            'ophinbiometry_lenstype_lens',
            'id'
        );
        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_r_fk',
            'et_ophinbiometry_selection',
            'lens_id_right',
            'ophinbiometry_lenstype_lens',
            'id'
        );

        //Rename the table last
        $this->renameTable('et_ophinbiometry_lenstype', 'et_ophinbiometry_measurement');
        $this->renameTable('et_ophinbiometry_lenstype_version', 'et_ophinbiometry_measurement_version');

        //Update element
        $this->update(
            'element_type',
            array('class_name' => 'Element_OphInBiometry_Measurement', 'name' => 'Measurements'),
            'class_name="Element_OphInBiometry_LensType"'
        );

        //Update view
        $this->execute('CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS SELECT
							eol.id, eol.eye_id, eol.last_modified_date, target_refraction_left, target_refraction_right,
							(SELECT name FROM ophinbiometry_lenstype_lens oll WHERE oll.id=eos.lens_id_left) as lens_left,
							(SELECT description FROM ophinbiometry_lenstype_lens oll WHERE oll.id=eos.lens_id_left) as lens_description_left,
							(SELECT acon FROM ophinbiometry_lenstype_lens oll WHERE oll.id=eos.lens_id_left) AS lens_acon_left,
							(SELECT name FROM ophinbiometry_lenstype_lens oll WHERE oll.id=eos.lens_id_right) as lens_right,
							(SELECT description FROM ophinbiometry_lenstype_lens oll WHERE oll.id=eos.lens_id_right) as lens_description_right,
							(SELECT acon FROM ophinbiometry_lenstype_lens oll WHERE oll.id=eos.lens_id_right) AS lens_acon_right,
							k1_left, k1_right, k2_left, k2_right, axis_k1_left, axis_k1_right, axial_length_left, axial_length_right,
							snr_left, snr_right, iol_power_left, iol_power_right, predicted_refraction_left, predicted_refraction_right, patient_id
							FROM et_ophinbiometry_measurement eol
							JOIN et_ophinbiometry_calculation eoc ON eoc.event_id=eol.event_id
							JOIN et_ophinbiometry_selection eos ON eos.event_id=eol.event_id
							JOIN event ev ON ev.id=eol.event_id
							JOIN episode ep ON ep.id=ev.episode_id
							ORDER BY eol.last_modified_date;');
    }

    public function down()
    {
        //Rename the table first
        $this->renameTable('et_ophinbiometry_measurement', 'et_ophinbiometry_lenstype');
        $this->renameTable('et_ophinbiometry_measurement_version', 'et_ophinbiometry_lenstype_version');

        //Drop key
        $this->dropForeignKey('ophinbiometry_lenstype_lens_l_fk', 'et_ophinbiometry_selection');
        $this->dropForeignKey('ophinbiometry_lenstype_lens_r_fk', 'et_ophinbiometry_selection');

        //Drop and add on main table
        $this->dropColumn('et_ophinbiometry_selection', 'lens_id_left');
        $this->dropColumn('et_ophinbiometry_selection', 'lens_id_right');
        $this->addColumn('et_ophinbiometry_lenstype', 'lens_id_left', 'int(10) unsigned NOT NULL DEFAULT 1');
        $this->addColumn('et_ophinbiometry_lenstype', 'lens_id_right', 'int(10) unsigned NOT NULL DEFAULT 1');

        //Drop and add on version tables
        $this->dropColumn('et_ophinbiometry_selection_version', 'lens_id_left');
        $this->dropColumn('et_ophinbiometry_selection_version', 'lens_id_right');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'lens_id_left', 'int(10) unsigned NOT NULL DEFAULT 1');
        $this->addColumn('et_ophinbiometry_lenstype_version', 'lens_id_right', 'int(10) unsigned NOT NULL DEFAULT 1');

        //Add new key
        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_l_fk',
            'et_ophinbiometry_lenstype',
            'lens_id_left',
            'ophinbiometry_lenstype_lens',
            'id'
        );
        $this->addForeignKey(
            'ophinbiometry_lenstype_lens_r_fk',
            'et_ophinbiometry_lenstype',
            'lens_id_right',
            'ophinbiometry_lenstype_lens',
            'id'
        );

        //Update element
        $this->update(
            'element_type',
            array('class_name' => 'Element_OphInBiometry_LensType', 'name' => 'Lens Type'),
            'class_name="Element_OphInBiometry_Measurement"'
        );

        $this->execute('CREATE OR REPLACE VIEW et_ophtroperationnote_biometry AS SELECT
							eol.id, eol.eye_id, eol.last_modified_date, target_refraction_left, target_refraction_right,
							(SELECT name FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_left) as lens_left,
							(SELECT description FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_left) as lens_description_left,
							(SELECT acon FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_left) AS lens_acon_left,
							(SELECT name FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_right) as lens_right,
							(SELECT description FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_right) as lens_description_right,
							(SELECT acon FROM ophinbiometry_lenstype_lens oll WHERE oll.id=lens_id_right) AS lens_acon_right,
							k1_left, k1_right, k2_left, k2_right, axis_k1_left, axis_k1_right, axial_length_left, axial_length_right,
							snr_left, snr_right, iol_power_left, iol_power_right, predicted_refraction_left, predicted_refraction_right, patient_id
							FROM et_ophinbiometry_lenstype eol
							JOIN et_ophinbiometry_calculation eoc ON eoc.event_id=eol.event_id
							JOIN et_ophinbiometry_selection eos ON eos.event_id=eol.event_id
							JOIN event ev ON ev.id=eol.event_id
							JOIN episode ep ON ep.id=ev.episode_id
							ORDER BY eol.last_modified_date;');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
