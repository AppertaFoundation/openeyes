<?php

class m151221_122711_add_additional_measurements extends CDbMigration
{
    public function up()
    {
        $this->addColumn('et_ophinbiometry_measurement', 'al_modified_left', 'boolean default false');
        $this->addColumn('et_ophinbiometry_measurement', 'al_modified_right', 'boolean default false');
        $this->addColumn('et_ophinbiometry_measurement', 'k_modified_left', 'boolean default false');
        $this->addColumn('et_ophinbiometry_measurement', 'k_modified_right', 'boolean default false');

        $this->addColumn('et_ophinbiometry_measurement_version', 'al_modified_left', 'boolean default false after  eye_status_right');
        $this->addColumn('et_ophinbiometry_measurement_version', 'al_modified_right', 'boolean default false after eye_status_right');
        $this->addColumn('et_ophinbiometry_measurement_version', 'k_modified_left', 'boolean default false after eye_status_right');
        $this->addColumn('et_ophinbiometry_measurement_version', 'k_modified_right', 'boolean default false after eye_status_right');
    }

    public function down()
    {
        $this->dropColumn('et_ophinbiometry_measurement', 'al_modified_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'al_modified_right');
        $this->dropColumn('et_ophinbiometry_measurement', 'k_modified_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'k_modified_right');

        $this->dropColumn('et_ophinbiometry_measurement_version', 'al_modified_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'al_modified_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'k_modified_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'k_modified_right');
    }
}
