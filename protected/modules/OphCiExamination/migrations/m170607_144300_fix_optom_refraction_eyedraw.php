<?php

class m170607_144300_fix_optom_refraction_eyedraw extends CDbMigration
{
    public function up()
    {
        $this->execute("UPDATE et_ophciexamination_refraction
                        SET    left_axis_eyedraw = CONCAT('[{\"scaleLevel\": 1,\"version\":1.1,\"subclass\":\"TrialLens\",\"rotation\":', 180 - left_axis, ',\"order\":0},{\"scaleLevel\": 1,\"version\":1.1,\"subclass\":\"TrialFrame\",\"order\":1}]')
                        WHERE  left_axis IS NOT NULL
                          AND  left_axis_eyedraw IS NULL
                          AND  event_id IN (SELECT id FROM event WHERE is_automated = 1 AND event_type_id = (SELECT id FROM event_type WHERE name = 'Examination'))");

        $this->execute("UPDATE et_ophciexamination_refraction
                        SET    right_axis_eyedraw = CONCAT('[{\"scaleLevel\": 1,\"version\":1.1,\"subclass\":\"TrialLens\",\"rotation\":', 180 - right_axis, ',\"order\":0},{\"scaleLevel\": 1,\"version\":1.1,\"subclass\":\"TrialFrame\",\"order\":1}]')
                        WHERE  right_axis IS NOT NULL
                          AND  right_axis_eyedraw IS NULL
                          AND  event_id IN (SELECT id FROM event WHERE is_automated = 1 AND event_type_id = (SELECT id FROM event_type WHERE name = 'Examination'))");
    }

    public function down()
    {
    }

}
