<?php

class m151211_120037_update_selection_field extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophinbiometry_selection', 'iol_power_left', 'varchar(15) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection', 'iol_power_right', 'varchar(15) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection', 'predicted_refraction_left', 'varchar(15) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection', 'predicted_refraction_right', 'varchar(15) not null default 0.00');

        $this->alterColumn('et_ophinbiometry_selection_version', 'iol_power_left', 'varchar(15) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection_version', 'iol_power_right', 'varchar(15) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection_version', 'predicted_refraction_left', 'varchar(15) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection_version', 'predicted_refraction_right', 'varchar(15) not null default 0.00');
    }

    public function down()
    {
        $this->alterColumn('et_ophinbiometry_selection', 'iol_power_left', ' decimal(6,2) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection', 'iol_power_right', ' decimal(6,2) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection', 'predicted_refraction_left', ' decimal(6,2) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection', 'predicted_refraction_right', ' decimal(6,2) not null default 0.00');

        $this->alterColumn('et_ophinbiometry_selection_version', 'iol_power_left', ' decimal(6,2) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection_version', 'iol_power_right', ' decimal(6,2) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection_version', 'predicted_refraction_left', ' decimal(6,2) not null default 0.00');
        $this->alterColumn('et_ophinbiometry_selection_version', 'predicted_refraction_right', ' decimal(6,2) not null default 0.00');
    }
}
