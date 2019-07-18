<?php

class m170305_160630_add_keratoconus_data extends OEMigration
{
    public function up()
    {
        $this->insert('ophtroperationnote_cxl_iontophoresis', array(
            'name' => 'Yes',
            'display_order' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_iontophoresis', array(
            'name' => 'No',
            'display_order' => 2,
        ));


        $this->insert('ophciexamination_cxl_ocular_surface_disease', array(
            'name' => 'HSK',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_cxl_ocular_surface_disease', array(
            'name' => 'Bacterial Keratitis',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_cxl_ocular_surface_disease', array(
            'name' => 'Marginal Keratitis',
            'display_order' => 3,
        ));
        $this->insert('ophciexamination_cxl_ocular_surface_disease', array(
            'name' => 'RCES',
            'display_order' => 4,
        ));
        $this->insert('ophciexamination_cxl_ocular_surface_disease', array(
            'name' => 'Allergic Eye Disease',
            'display_order' => 5,
        ));

        $this->insert('ophciexamination_cxl_cl_removed', array(
            'name' => 'N/A',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_cxl_cl_removed', array(
            'name' => 'Today',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_cxl_cl_removed', array(
            'name' => '1 Week',
            'display_order' => 3,
        ));
        $this->insert('ophciexamination_cxl_cl_removed', array(
            'name' => '2 Weeks',
            'display_order' => 4,
        ));

        $this->insert('ophciexamination_scan_quality', array(
            'name' => 'Good',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_scan_quality', array(
            'name' => 'Poor',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_scan_quality', array(
            'name' => 'Failed',
            'display_order' => 3,
        ));
        $this->insert('ophciexamination_scan_quality', array(
            'name' => 'Unknown',
            'display_order' => 4,
        ));

        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'None',
            'display_order' => 1,
            'active' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'Sterile Infiltrate',
            'display_order' => 2,
            'active' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'Infective Infiltrate',
            'display_order' => 3,
            'active' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'Melt',
            'display_order' => 4,
            'active' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'Delayed Epithelial Healing',
            'display_order' => 5,
            'active' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'HSK re-activation',
            'display_order' => 6,
            'active' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'RCES',
            'display_order' => 7,
            'active' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_complications', array(
            'name' => 'Haze',
            'display_order' => 8,
            'active' => 1,
        ));

        $this->insert('ophciexamination_slit_lamp_cornea', array(
            'name' => 'Clear',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_slit_lamp_cornea', array(
            'name' => 'Scarring',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_slit_lamp_cornea', array(
            'name' => 'Other',
            'display_order' => 3,
        ));


        $this->insert('ophciexamination_cxl_quality_score', array(
            'name' => 'OK',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_cxl_quality_score', array(
            'name' => 'Borderline',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_cxl_quality_score', array(
            'name' => 'Poor',
            'display_order' => 3,
        ));

        $this->insert('ophciexamination_slit_lamp_conditions', array(
            'name' => 'None',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_slit_lamp_conditions', array(
            'name' => 'Controlled',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_slit_lamp_conditions', array(
            'name' => 'Uncontrolled',
            'display_order' => 3,
        ));

        $this->insert('ophciexamination_specular_microscope', array(
            'name' => 'Konan',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_specular_microscope', array(
            'name' => 'Topcon',
            'display_order' => 2,
        ));

        $this->insert('ophciexamination_tomographer_device', array(
            'name' => 'Pentacam',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_tomographer_device', array(
            'name' => 'RTVue',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_tomographer_device', array(
            'name' => 'Visante',
            'display_order' => 3,
        ));
        $this->insert('ophciexamination_tomographer_device', array(
            'name' => 'Casia',
            'display_order' => 4,
        ));


        $this->insert('ophciexamination_cxl_outcome_diagnosis', array(
            'name' => 'Not Ectasia',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_cxl_outcome_diagnosis', array(
            'name' => 'Keratoconus',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_cxl_outcome_diagnosis', array(
            'name' => 'Post-Laser Ectasia',
            'display_order' => 3,
        ));
        $this->insert('ophciexamination_cxl_outcome_diagnosis', array(
            'name' => 'Pellucid Marginal Degeneration',
            'display_order' => 4,
        ));


        $this->insert('ophciexamination_topographer_device', array(
            'name' => 'Pentacam',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_topographer_device', array(
            'name' => 'Topcon',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_topographer_device', array(
            'name' => 'Opticon',
            'display_order' => 3,
        ));
        $this->insert('ophciexamination_topographer_device', array(
            'name' => 'Zeiss',
            'display_order' => 4,
        ));

        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter', array(
            'name' => '6mm',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter', array(
            'name' => '7mm',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter', array(
            'name' => '8mm',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter', array(
            'name' => '9mm',
            'display_order' => 4,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter', array(
            'name' => '10mm',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter_version', array(
            'name' => '6mm',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter_version', array(
            'name' => '7mm',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter_version', array(
            'name' => '8mm',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter_version', array(
            'name' => '9mm',
            'display_order' => 4,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_diameter_version', array(
            'name' => '10mm',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_epithelial_removal_method', array(
            'name' => 'Manual',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method', array(
            'name' => 'Alcohol Assisted Manual',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method', array(
            'name' => 'Trans-PTK',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method', array(
            'name' => 'Epithelium Disrupted',
            'display_order' => 4,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method', array(
            'name' => 'Epithelium-on',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method', array(
            'name' => 'Trans-PRK',
            'display_order' => 6,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_epithelial_removal_method_version', array(
            'name' => 'Manual',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method_version', array(
            'name' => 'Alcohol Assisted Manual',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method_version', array(
            'name' => 'Trans-PTK',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method_version', array(
            'name' => 'Epithelium Disrupted',
            'display_order' => 4,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method_version', array(
            'name' => 'Epithelium-on',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_epithelial_removal_method_version', array(
            'name' => 'Trans-PRK',
            'display_order' => 6,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '0 seconds',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '0.5 seconds',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '1 second',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '1.5 seconds',
            'display_order' => 4,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '2 seconds',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '2.5 seconds',
            'display_order' => 6,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '3 seconds',
            'display_order' => 7,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '3.5 seconds',
            'display_order' => 8,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '4 seconds',
            'display_order' => 9,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '4.5 seconds',
            'display_order' => 10,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration', array(
            'name' => '5 seconds',
            'display_order' => 11,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '0 seconds',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '0.5 seconds',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '1 seconds',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '1.5 seconds',
            'display_order' => 4,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '2 seconds',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '2.5 seconds',
            'display_order' => 6,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '3 seconds',
            'display_order' => 7,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '3.5 seconds',
            'display_order' => 8,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '4 seconds',
            'display_order' => 9,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '4.5 seconds',
            'display_order' => 10,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interpulse_duration_version', array(
            'name' => '5 seconds',
            'display_order' => 11,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_interval_between_drops', array(
            'name' => '1 minute',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interval_between_drops', array(
            'name' => '2 minutes',
            'display_order' => 2,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_interval_between_drops', array(
            'name' => '3 minutes',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_interval_between_drops_version', array(
            'name' => '1 minute',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_interval_between_drops_version', array(
            'name' => '2 minutes',
            'display_order' => 2,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_interval_between_drops_version', array(
            'name' => '3 minutes',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_protocol', array(
            'name' => 'Avedro Rapid Pulsed',
            'display_order' => 1,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_protocol', array(
            'name' => 'Avedro Rapid',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_protocol', array(
            'name' => 'Dresden',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_protocol', array(
            'name' => 'LASIK Extra',
            'display_order' => 4,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_protocol_version', array(
            'name' => 'Avedro Rapid Pulsed',
            'display_order' => 1,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_protocol_version', array(
            'name' => 'Avedro Rapid',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_protocol_version', array(
            'name' => 'Dresden',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_protocol_version', array(
            'name' => 'LASIK Extra',
            'display_order' => 4,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_riboflavin_preparation', array(
            'name' => 'Vibex Rapid',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_riboflavin_preparation', array(
            'name' => 'Vibex Extra',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));

        $this->insert('ophtroperationnote_cxl_riboflavin_preparation_version', array(
            'name' => 'Vibex Rapid',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_riboflavin_preparation_version', array(
            'name' => 'Vibex Extra',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));

        $columns = 30;
        for ($k = 1 ; $k < $columns; $k++){
            if($k == 10){
                $defaultChoice = 1;
            }else{
                $defaultChoice = 0;
            }
            $this->insert('ophtroperationnote_cxl_soak_duration', array(
                'name' => $k . ' minutes',
                'display_order' => $k,
                'defaultChoice' => $defaultChoice,
            ));
            $this->insert('ophtroperationnote_cxl_soak_duration_version', array(
                'name' => $k . ' minutes',
                'display_order' => $k,
                'defaultChoice' => $defaultChoice,
            ));
        }

        $columns = 30;
        for ($k = 0 ; $k < $columns; $k++){
            if($k == 8){
                $defaultChoice = 1;
            }else{
                $defaultChoice = 0;
            }
            $this->insert('ophtroperationnote_cxl_total_exposure_time', array(
                'name' => $k . ' minutes',
                'display_order' => $k+1,
                'defaultChoice' => $defaultChoice,
            ));
            $this->insert('ophtroperationnote_cxl_total_exposure_time_version', array(
                'name' => $k . ' minutes',
                'display_order' => $k+1,
                'defaultChoice' => $defaultChoice,
            ));
        }

        $columns = 40;
        for ($k = 3 ; $k < $columns; $k++){
            if($k == 30){
                $defaultChoice = 1;
            }else{
                $defaultChoice = 0;
            }
            $this->insert('ophtroperationnote_cxl_uv_irradiance', array(
                'name' => $k,
                'display_order' => $k-2,
                'defaultChoice' => $defaultChoice,
            ));
            $this->insert('ophtroperationnote_cxl_uv_irradiance_version', array(
                'name' => $k . ' minutes',
                'display_order' => $k-2,
                'defaultChoice' => $defaultChoice,
            ));
        }

        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '0 seconds',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '0.5 seconds',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '1 seconds',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '1.5 seconds',
            'display_order' => 4,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '2 seconds',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '2.5 seconds',
            'display_order' => 6,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '3 seconds',
            'display_order' => 7,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '3.5 seconds',
            'display_order' => 8,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '4 seconds',
            'display_order' => 9,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '4.5 seconds',
            'display_order' => 10,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration', array(
            'name' => '5 seconds',
            'display_order' => 11,
            'defaultChoice' => 0,
        ));


        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '0 seconds',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '0.5 seconds',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '1 seconds',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '1.5 seconds',
            'display_order' => 4,
            'defaultChoice' => 1,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '2 seconds',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '2.5 seconds',
            'display_order' => 6,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '3 seconds',
            'display_order' => 7,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '3.5 seconds',
            'display_order' => 8,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '4 seconds',
            'display_order' => 9,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '4.5 seconds',
            'display_order' => 10,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_uv_pulse_duration_version', array(
            'name' => '5 seconds',
            'display_order' => 11,
            'defaultChoice' => 0,
        ));

        $eventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphCiExamination'))
            ->queryScalar();

        $anteriorSegmentDisplayId = $this->dbConnection->createCommand()
            ->select('display_order')
            ->from('element_type')
            ->where('class_name=:class_name', array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_AnteriorSegment'))
            ->queryScalar();

        $anteriorSegmentDisplayId = $anteriorSegmentDisplayId + 1;

        $this->insert('element_type', array(
            'name' => 'Keratoconus Monitoring',
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CXL_History',
            'event_type_id' => $eventTypeId,
            'display_order' => $anteriorSegmentDisplayId,
        ));

        // get id for above for use below
        $keraMonTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name=:class_name', array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_CXL_History'))
            ->queryScalar();

        $this->insert('element_type', array(
            'name' => 'Corneal Tomography',
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Keratometry',
            'event_type_id' => $eventTypeId,
            'display_order' => 1,
            'parent_element_type_id' => $keraMonTypeId,
        ));
        $this->insert('element_type', array(
            'name' => 'Specular Microscopy',
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Specular_Microscopy',
            'event_type_id' => $eventTypeId,
            'display_order' => 2,
            'parent_element_type_id' => $keraMonTypeId,
        ));
        $this->insert('element_type', array(
            'name' => 'KC/CXL-Specific Slit Lamp',
            'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_Slit_Lamp',
            'event_type_id' => $eventTypeId,
            'display_order' => 3,
            'parent_element_type_id' => $keraMonTypeId,
        ));

        $opEventTypeId = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => 'OphTrOperationnote'))
            ->queryScalar();


        $this->insert('element_type', array(
            'name' => 'CXL (Cross-Linking)',
            'class_name' => 'Element_OphTrOperationnote_CXL',
            'event_type_id' => $opEventTypeId,
            'display_order' => 10,
            'parent_element_type_id' => 34,
        ));

        $this->insert('ophciexamination_cxl_outcome', array(
            'name' => '6 month review',
            'display_order' => 1,
        ));
        $this->insert('ophciexamination_cxl_outcome', array(
            'name' => '12 month review',
            'display_order' => 2,
        ));
        $this->insert('ophciexamination_cxl_outcome', array(
            'name' => 'List R CXL',
            'display_order' => 3,
        ));
        $this->insert('ophciexamination_cxl_outcome', array(
            'name' => 'List L CXL',
            'display_order' => 4,
        ));
        $this->insert('ophciexamination_cxl_outcome', array(
            'name' => 'List BE CXL',
            'display_order' => 5,
        ));
        $this->insert('ophciexamination_cxl_outcome', array(
            'name' => 'Discharge - CL Dependent',
            'display_order' => 6,
        ));
        $this->insert('ophciexamination_cxl_outcome', array(
            'name' => 'Discharge - Stable',
            'display_order' => 7,
        ));

        $this->insert('episode_summary_item', array(
            'event_type_id' => $eventTypeId,
            'name' => 'Keratometry',
        ));
        $this->insert('episode_summary_item', array(
            'event_type_id' => $eventTypeId,
            'name' => 'Keratometry Chart Right',
        ));
        $this->insert('episode_summary_item', array(
            'event_type_id' => $eventTypeId,
            'name' => 'Keratometry Chart Left',
        ));

        $this->insert('ophtroperationnote_cxl_devices', array(
            'name' => 'IROC UVX-1000',
            'display_order' => 1,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_devices', array(
            'name' => 'IROC UVX-2000',
            'display_order' => 2,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_devices', array(
            'name' => 'Avedro KXL I',
            'display_order' => 3,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_devices', array(
            'name' => 'Avedro KXL II',
            'display_order' => 4,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_devices', array(
            'name' => 'Peschke CCL-Vario',
            'display_order' => 5,
            'defaultChoice' => 0,
        ));
        $this->insert('ophtroperationnote_cxl_devices', array(
            'name' => 'Other',
            'display_order' => 6,
            'defaultChoice' => 0,
        ));


    }




    public function down()
    {
        echo "m170305_160630_add_keratoconus_data does not support migration down.\n";
        return false;
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