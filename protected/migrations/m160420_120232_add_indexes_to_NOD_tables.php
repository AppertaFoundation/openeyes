<?php

class m160420_120232_add_indexes_to_NOD_tables extends CDbMigration
{
    public function up()
    {
        //EpisodeDrug
            $this->createIndex('medication_prescription_item_id', 'medication', 'prescription_item_id');
        $this->createIndex('medication_last_modified_date', 'medication', 'last_modified_date');

            //EpisodeVisualAcuity
            $this->createIndex('ophciexamination_visual_acuity_unit_value_base_value', 'ophciexamination_visual_acuity_unit_value', 'base_value');
        $this->createIndex('ophciexamination_visual_acuity_unit_name', 'ophciexamination_visual_acuity_unit', 'name');
        $this->createIndex('ophciexamination_visualacuity_method_name', 'ophciexamination_visualacuity_method', 'name');
        $this->createIndex('ophciexamination_visualacuity_reading_side', 'ophciexamination_visualacuity_reading', 'side');
        $this->createIndex('et_ophciexamination_visualacuity_last_modified_date', 'et_ophciexamination_visualacuity', 'last_modified_date');

            // EpisodeIOP
            $this->createIndex('et_ophciexamination_intraocularpressure_last_modified_date', 'et_ophciexamination_intraocularpressure', 'last_modified_date');
    }

    public function down()
    {
        //EpisodeDrug
            $this->dropIndex('medication_prescription_item_id', 'medication');
        $this->dropIndex('medication_last_modified_date', 'medication');

            //EpisodeVisualAcuity
            $this->dropIndex('ophciexamination_visual_acuity_unit_value_base_value', 'ophciexamination_visual_acuity_unit_value');
        $this->dropIndex('ophciexamination_visual_acuity_unit_name', 'ophciexamination_visual_acuity_unit');
        $this->dropIndex('ophciexamination_visualacuity_method_name', 'ophciexamination_visualacuity_method');
        $this->dropIndex('ophciexamination_visualacuity_reading_side', 'ophciexamination_visualacuity_reading');
        $this->dropIndex('et_ophciexamination_visualacuity_last_modified_date', 'et_ophciexamination_visualacuity');

            // EpisodeIOP
            $this->dropIndex('et_ophciexamination_intraocularpressure_last_modified_date', 'et_ophciexamination_intraocularpressure');
    }
}
