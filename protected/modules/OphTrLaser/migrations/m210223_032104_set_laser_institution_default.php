<?php

class m210223_032104_set_laser_institution_default extends OEMigration
{
    public function safeUp()
    {
        $institution_id = $this->dbConnection
            ->createCommand("SELECT id FROM institution WHERE remote_id = '" . Yii::app()->params['institution_code'] . "'")
            ->queryScalar();

        $this->execute("UPDATE ophtrlaser_site_laser SET institution_id = :institution_id WHERE institution_id IS NULL", [':institution_id' => $institution_id]);
    }

    public function safeDown()
    {
        echo("Down not supported. Manually recitify which laser belong to which institution if necessary");
    }
}
