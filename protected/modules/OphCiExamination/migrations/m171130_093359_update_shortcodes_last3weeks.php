<?php

class m171130_093359_update_shortcodes_last3weeks extends CDbMigration
{
    public function up()
    {
        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingBothLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipb')
        );

        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingLeftLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipl')
        );

        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingPrincipalLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipp')
        );

        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingRightLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipr')
        );

        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingAbbrLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ipa')
        );

        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingRightNoUnitsLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'ior')
        );

        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingLeftNoUnitsLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'iol')
        );

        $this->update('patient_shortcode',
            $set = array(
                    'method' => 'getLetterIOPReadingBothFirstLast3weeks',
                    'description' => new CDbExpression('CONCAT(description, " (Latest recorded within the last 3 weeks)")')
            ),
            $condition = 'code = :code',
            $params = array(':code' => 'iof')
        );

    }

    public function down()
    {
        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingBoth',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "ipb")
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingLeft',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "ipl")
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingPrincipal',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "ipp")
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingRight',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "ipr")
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingAbbr',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "ipa")
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getIOPReadingRightNoUnits',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "ior")
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getIOPReadingLeftNoUnits',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "iol")
        );

        $this->update('patient_shortcode',
            $set = array(
                'method' => 'getLetterIOPReadingBothFirst',
                'description' => new CDbExpression('REPLACE(description, " (Latest recorded within the last 3 weeks)", "")')
            ),
            $condition = 'code = :code',
            array(":code" => "iof")
        );
    }
}