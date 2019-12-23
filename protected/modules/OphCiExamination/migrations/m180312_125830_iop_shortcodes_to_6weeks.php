<?php

class m180312_125830_iop_shortcodes_to_6weeks extends CDbMigration
{
    public function up()
    {
        $this->update('patient_shortcode',
            array(
                'method' => 'getLetterIOPReadingBothLast6weeks',
                'description' => 'Intraocular pressure in both eyes (Latest recorded within the last 6 weeks)',
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipb')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingLeftLast6weeks',
                'description' => 'Intraocular pressure in the left eye (Latest recorded within the last 6 weeks)',
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipl')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingPrincipalLast6weeks',
                'description' => 'Intraocular pressure in the principal eye (Latest recorded within the last 6 weeks)',
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipp')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingRightLast6weeks',
                'description' => 'Intraocular pressure in the right eye (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipr')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingAbbrLast6weeks',
                'description' => 'Intraocular pressure, abbreviated form (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipa')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingRightNoUnitsLast6weeks',
                'description' => 'Intraocular pressure, right eye reading no units (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ior')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingLeftNoUnitsLast6weeks',
                'description' => 'Intraocular pressure, left eye reading no units (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'iol')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingBothFirstLast6weeks',
                'description' => 'Intraocular pressure, both eyes, first reading only (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'iof')
        );

        // VA shortcodes

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityBothLast6weeks',
                'description' => 'Best visual acuity in both eyes with date (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'bvd')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityLeftLast6weeks',
                'description' => 'Best visual acuity in the left eye with date (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'lvd')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityPrincipalLast6weeks',
                'description' => 'Best visual acuity in the principle eye with date (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'pvd')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityRightLast6weeks',
                'description' => 'Best visual acuity in the right eye with date (Latest recorded within the last 6 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'rvd')
        );
    }

    public function down()
    {
        $this->update('patient_shortcode',
            array(
                'method' => 'getLetterIOPReadingBothLast3weeks',
                'description' => 'Intraocular pressure in both eyes (Latest recorded within the last 3 weeks)',
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipb')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingLeftLast3weeks',
                'description' => 'Intraocular pressure in the left eye (Latest recorded within the last 3 weeks)',
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipl')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingPrincipalLast3weeks',
                'description' => 'Intraocular pressure in the principal eye (Latest recorded within the last 3 weeks)',
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipp')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingRightLast3weeks',
                'description' => 'Intraocular pressure in the right eye (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipr')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingAbbrLast3weeks',
                'description' => 'Intraocular pressure, abbreviated form (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipa')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingRightNoUnitsLast3weeks',
                'description' => 'Intraocular pressure, right eye reading no units (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ior')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingLeftNoUnitsLast3weeks',
                'description' => 'Intraocular pressure, left eye reading no units (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'iol')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingBothFirstLast3weeks',
                'description' => 'Intraocular pressure, both eyes, first reading only (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'iof')
        );

        // VA shortcodes

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityBothLast3weeks',
                'description' => 'Best visual acuity in both eyes with date (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'bvd')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityLeftLast3weeks',
                'description' => 'Best visual acuity in the left eye with date (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'lvd')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityPrincipalLast3weeks',
                'description' => 'Best visual acuity in the principle eye with date (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'pvd')
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterVisualAcuityRightLast3weeks',
                'description' => 'Best visual acuity in the right eye with date (Latest recorded within the last 3 weeks)'
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'rvd')
        );
    }
}