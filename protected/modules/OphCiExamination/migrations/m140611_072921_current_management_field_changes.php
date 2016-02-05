<?php

class m140611_072921_current_management_field_changes extends CDbMigration
{
    public function up()
    {
        $this->renameColumn('et_ophciexamination_currentmanagementplan', 'left_other-service', 'left_other_service');
        $this->renameColumn('et_ophciexamination_currentmanagementplan', 'right_other-service', 'right_other_service');

        $this->renameColumn('et_ophciexamination_currentmanagementplan_version', 'left_other-service', 'left_other_service');
        $this->renameColumn('et_ophciexamination_currentmanagementplan_version', 'right_other-service', 'right_other_service');

        foreach (array('other_service', 'refraction', 'lva', 'orthoptics', 'cl_clinic', 'vf', 'us', 'biometry', 'oct', 'hrt', 'disc_photos', 'edt') as $field) {
            $this->dropColumn('et_ophciexamination_currentmanagementplan', 'left_'.$field);
            $this->renameColumn('et_ophciexamination_currentmanagementplan', 'right_'.$field, $field);

            $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'left_'.$field);
            $this->renameColumn('et_ophciexamination_currentmanagementplan_version', 'right_'.$field, $field);
        }
    }

    public function down()
    {
        foreach (array('other_service', 'refraction', 'lva', 'orthoptics', 'cl_clinic', 'vf', 'us', 'biometry', 'oct', 'hrt', 'disc_photos', 'edt') as $field) {
            $this->dropColumn('et_ophciexamination_currentmanagementplan', 'left_'.$field);
            $this->renameColumn('et_ophciexamination_currentmanagementplan', 'right_'.$field, $field);

            $this->dropColumn('et_ophciexamination_currentmanagementplan_version', 'left_'.$field);
            $this->renameColumn('et_ophciexamination_currentmanagementplan_version', 'right_'.$field, $field);
        }

        $this->renameColumn('et_ophciexamination_currentmanagementplan', 'left_other_service', 'left_other-service');
        $this->renameColumn('et_ophciexamination_currentmanagementplan', 'right_other_service', 'right_other-service');

        $this->renameColumn('et_ophciexamination_currentmanagementplan_version', 'left_other_service', 'left_other-service');
        $this->renameColumn('et_ophciexamination_currentmanagementplan_version', 'right_other_service', 'right_other-service');
    }
}
