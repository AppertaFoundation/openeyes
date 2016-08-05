<?php

class m141217_140957_procedure_pdf_tag extends CDbMigration
{
    public function up()
    {
        $et = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphTrConsent'))->queryScalar();

        $this->insert('pdf_footer_tag', array(
            'event_type_id' => $et,
            'tag_name' => 'PROCEDURES',
            'method' => 'getFooterProcedures',
        ));
    }

    public function down()
    {
        $et = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = :class_name', array(':class_name' => 'OphTrConsent'))->queryScalar();

        $this->delete('pdf_footer_tag', "event_type_id = $et and tag_name = 'PROCEDURES'");
    }
}
