<?php

class m151209_110519_add_additional_dicom_biometryreports_data extends CDbMigration
{
    public function up()
    {

        //K2 Axis (k2_axis_left, k2_axis_right)
        $this->addColumn('et_ophinbiometry_measurement', 'k2_axis_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'k2_axis_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement', 'k2_axis_right', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'k2_axis_right', 'decimal(6,1) not null DEFAULT 0.0');

        //* &Delta;K (this is chown as 'Cyl.' on the printout (delta_k_left, delta_k_right)
        $this->addColumn('et_ophinbiometry_measurement', 'delta_k_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'delta_k_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement', 'delta_k_right', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'delta_k_right', 'decimal(6,2) not null DEFAULT 0.00');

        //* &Delta;K axis (delta_k_axis_left, delta_k_axis_right)
        $this->addColumn('et_ophinbiometry_measurement', 'delta_k_axis_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'delta_k_axis_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement', 'delta_k_axis_right', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'delta_k_axis_right', 'decimal(6,1) not null DEFAULT 0.0');

        //* ACD (acd_left, acd_right)
        $this->addColumn('et_ophinbiometry_measurement', 'acd_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'acd_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement', 'acd_right', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'acd_right', 'decimal(6,2) not null DEFAULT 0.00');

        //* Refraction (sphere, cyl and axis) (refraction_sphere_left, refraction_sphere_right, refraction_delta_left, refraction_delta_right, refraction_axis_left, refraction_axis_right)

        $this->addColumn('et_ophinbiometry_measurement', 'refraction_sphere_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'refraction_sphere_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement', 'refraction_sphere_right', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'refraction_sphere_right', 'decimal(6,2) not null DEFAULT 0.00');

        $this->addColumn('et_ophinbiometry_measurement', 'refraction_delta_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'refraction_delta_left', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement', 'refraction_delta_right', 'decimal(6,2) not null DEFAULT 0.00');
        $this->addColumn('et_ophinbiometry_measurement_version', 'refraction_delta_right', 'decimal(6,2) not null DEFAULT 0.00');

        $this->addColumn('et_ophinbiometry_measurement', 'refraction_axis_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'refraction_axis_left', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement', 'refraction_axis_right', 'decimal(6,1) not null DEFAULT 0.0');
        $this->addColumn('et_ophinbiometry_measurement_version', 'refraction_axis_right', 'decimal(6,1) not null DEFAULT 0.0');

        //EyeStatus (this is shown as 'Status on the printout) (eye_status_left, eye_status_right)

        $this->addColumn('et_ophinbiometry_measurement', 'eye_status_left', 'varchar(255)');
        $this->addColumn('et_ophinbiometry_measurement_version', 'eye_status_left', 'varchar(255)');
        $this->addColumn('et_ophinbiometry_measurement', 'eye_status_right', 'varchar(255)');
        $this->addColumn('et_ophinbiometry_measurement_version', 'eye_status_right', 'varchar(255)');

        $this->addColumn('et_ophinbiometry_calculation', 'comments', 'varchar(1000)');
        $this->addColumn('et_ophinbiometry_calculation_version', 'comments', 'varchar(1000)');
    }

    public function down()
    {

        //K2 Axis (k2_axis_left, k2_axis_right)
        $this->dropColumn('et_ophinbiometry_measurement', 'k2_axis_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'k2_axis_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'k2_axis_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'k2_axis_right');

        //* &Delta;K (this is chown as 'Cyl.' on the printout (delta_k_left, delta_k_right)
        $this->dropColumn('et_ophinbiometry_measurement', 'delta_k_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'delta_k_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'delta_k_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'delta_k_right');

        //* &Delta;K axis (delta_k_axis_left, delta_k_axis_right)
        $this->dropColumn('et_ophinbiometry_measurement', 'delta_k_axis_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'delta_k_axis_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'delta_k_axis_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'delta_k_axis_right');

        //* ACD (acd_left, acd_right)
        $this->dropColumn('et_ophinbiometry_measurement', 'acd_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'acd_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'acd_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'acd_right');

        //* Refraction (sphere, cyl and axis) (refraction_sphere_left, refraction_sphere_right, refraction_delta_left, refraction_delta_right, refraction_axis_left, refraction_axis_right)

        $this->dropColumn('et_ophinbiometry_measurement', 'refraction_sphere_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'refraction_sphere_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'refraction_sphere_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'refraction_sphere_right');

        $this->dropColumn('et_ophinbiometry_measurement', 'refraction_delta_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'refraction_delta_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'refraction_delta_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'refraction_delta_right');

        $this->dropColumn('et_ophinbiometry_measurement', 'refraction_axis_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'refraction_axis_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'refraction_axis_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'refraction_axis_right');

        //EyeStatus (this is shown as 'Status on the printout) (eye_status_left, eye_status_right)

        $this->dropColumn('et_ophinbiometry_measurement', 'eye_status_left');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'eye_status_left');
        $this->dropColumn('et_ophinbiometry_measurement', 'eye_status_right');
        $this->dropColumn('et_ophinbiometry_measurement_version', 'eye_status_right');

        $this->dropColumn('et_ophinbiometry_calculation', 'comments');
        $this->dropColumn('et_ophinbiometry_calculation_version', 'comments');
    }
}
