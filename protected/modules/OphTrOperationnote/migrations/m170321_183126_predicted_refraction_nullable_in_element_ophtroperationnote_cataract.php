<?php

class m170321_183126_predicted_refraction_nullable_in_element_ophtroperationnote_cataract extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('et_ophtroperationnote_cataract', 'predicted_refraction', 'decimal(4,2) NULL');
        $this->alterColumn('et_ophtroperationnote_cataract','iol_power','VARCHAR(5) NULL');
    }

    public function down()
    {
        $this->alterColumn('et_ophtroperationnote_cataract', 'predicted_refraction', 'decimal(4,2) NOT NULL');
        $this->alterColumn('et_ophtroperationnote_cataract','iol_power','VARCHAR(5) NOT NULL');
    }

}