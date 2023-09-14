<?php

class m230821_150443_adjust_refractive_target_from_recent_csm extends OEMigration
{
    public function safeUp()
    {
        $time_two_years_in_the_past = strtotime("-2 year", time());
        $date_two_years_in_the_past = date("Y-m-d", $time_two_years_in_the_past);

        foreach (['right', 'left'] as $eye_side) {
            $this->dbConnection->createCommand("
                   UPDATE et_ophinbiometry_calculation calc
                    SET calc.target_refraction_$eye_side = NULL
                    WHERE calc.event_id IN (SELECT calcsub.event_id FROM et_ophinbiometry_calculation calcsub
                    INNER JOIN v_patient_events vpe  ON vpe.event_id = calcsub.event_id
                                                            AND vpe.event_date > :date_two_years_in_the_past
                    WHERE calcsub.`target_refraction_$eye_side` != (
                    SELECT catman.left_target_postop_refraction FROM `et_ophciexamination_cataractsurgicalmanagement` catman
                    INNER JOIN v_patient_events vpecat ON vpecat.event_id = catman.event_id AND vpe.patient_id = vpecat.patient_id
                    ORDER BY vpecat.event_date DESC
                    LIMIT 1))")->execute([':date_two_years_in_the_past' => $date_two_years_in_the_past]);
        }
    }

    public function safeDown()
    {
        echo "m230821_150443_adjust_refractive_target_from_recent_csm does not support migration down.\n";
        return false;
    }
}
