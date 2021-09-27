<?php

class m210902_074011_adding_consent_form_extra_procedure_tables extends OEMigration
{
    public function up()
    {
        $this->dropForeignKey('et_ophtrconsent_extraprocedure_eye_id_fk', 'et_ophtrconsent_extraprocedure');
        $this->dropForeignKey('et_ophtrconsent_extraprocedure_booking_event_id_fk', 'et_ophtrconsent_extraprocedure');
        $this->dropForeignKey('et_ophtrconsent_extraprocedure_anaesthetic_type_id_fk', 'et_ophtrconsent_extraprocedure');

        $this->dropOEColumn('et_ophtrconsent_extraprocedure', 'eye_id', true);
        $this->dropOEColumn('et_ophtrconsent_extraprocedure', 'anaesthetic_type_id', true);
        $this->dropOEColumn('et_ophtrconsent_extraprocedure', 'booking_event_id', true);

        $this->createOETable(
            'ophtrconsent_procedure_extra_assignment',
            array(
                'id' => 'pk',
                'element_id' => 'int(11) NULL',
                'extra_proc_id' => 'int(11) NULL',

                'CONSTRAINT ophtrconsent_procedure_extra_assignment_elem_fk FOREIGN KEY (element_id) REFERENCES et_ophtrconsent_extraprocedure (id)',
                'CONSTRAINT oophtrconsent_procedure_extra_assignment_eproc_fk FOREIGN KEY (extra_proc_id) REFERENCES ophtrconsent_procedure_extra (id)',
            ),
            true
        );

        $this->update('element_type', array('required' => '1'), '`class_name` = :element_class', array(':element_class' => 'Element_OphTrConsent_ExtraProcedures'));
    }

    public function down()
    {
        echo "m210902_074011_adding_consent_form_extra_procedure_tables does not support migration down.\n";
        return false;
    }
}
