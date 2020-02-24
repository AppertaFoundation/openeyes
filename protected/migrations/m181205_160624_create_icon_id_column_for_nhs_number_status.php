<?php

class m181205_160624_create_icon_id_column_for_nhs_number_status extends CDbMigration
{
    /**
     * @return bool|void
     * @throws CException
     */
    public function up()
    {
        $this->addColumn('nhs_number_verification_status', 'icon_id', 'int(11)');

        $grey_icon_id = $this->dbConnection->createCommand('SELECT id FROM icons WHERE class_name = "exclamation pro-theme"')
            ->queryScalar();
        $amber_icon_id = $this->dbConnection->createCommand('SELECT id FROM icons WHERE class_name = "exclamation-amber"')
            ->queryScalar();
        $green_icon_id = $this->dbConnection->createCommand('SELECT id FROM icons WHERE class_name = "exclamation-green"')
            ->queryScalar();
        $red_icon_id = $this->dbConnection->createCommand('SELECT id FROM icons WHERE class_name = "exclamation-red"')
            ->queryScalar();

        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $green_icon_id],
            'description = "Number present and verified"'
        );
        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $amber_icon_id],
            'description = "Number present but not traced"'
        );
        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $red_icon_id],
            'description = "Trace required"'
        );
        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $red_icon_id],
            'description = "Trace attempted - No match or multiple match found"'
        );
        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $red_icon_id],
            'description = "Trace needs to be resolved - (NHS Number or PATIENT detail conflict)"'
        );
        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $amber_icon_id],
            'description = "Trace in progress"'
        );
        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $grey_icon_id],
            'description = "Number not present and trace not required"'
        );
        $this->update(
            'nhs_number_verification_status',
            ['icon_id' => $grey_icon_id],
            'description = "Trace postponed (baby under six weeks old)"'
        );
    }

    public function down()
    {
        $this->dropColumn('nhs_number_verification_status', 'icon_id');
    }
}
