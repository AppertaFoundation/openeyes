<?php

class m190925_124122_add_element_consultant_signature extends OEMigration
{
	public function up()
	{
        $this->createOETable("et_ophcocvi_consultant_signature", [
            'id' => 'pk',
            'event_id' => 'INT(10) UNSIGNED',
            'protected_file_id' => 'INT(10) UNSIGNED',
            'signed_by_user_id' => 'INT(10) UNSIGNED',
            'signature_date' => 'DATE'
        ], true);

        $this->addForeignKey("fk_et_cviconsultant_signature_event", "et_ophcocvi_consultant_signature", "event_id", "event", "id");
        $this->addForeignKey("fk_et_cviconsultant_signature_pf", "et_ophcocvi_consultant_signature", "protected_file_id", "protected_file", "id");
        $this->addForeignKey("fk_et_cviconsultant_signature_sb", "et_ophcocvi_consultant_signature", "signed_by_user_id", "user", "id");

        $this->createElementType("OphCoCvi", "Health professional signature", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsultantSignature',
            'default' => true,
            'required' => true,
            'display_order' => 35,
            'version' => 1,
        ]);
	}

	public function down()
	{
        $this->execute("DELETE FROM event_type WHERE class_name = :class_name", array(":class_name" => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ConsultantSignature'));

        $this->dropForeignKey("fk_et_cviconsultant_signature_event", "et_ophcocvi_consultant_signature");
        $this->dropForeignKey("fk_et_cviconsultant_signature_pf", "et_ophcocvi_consultant_signature");
        $this->dropForeignKey("fk_et_cviconsultant_signature_sb", "et_ophcocvi_consultant_signature");

        $this->dropOETable("et_ophcocvi_consultant_signature", true);
	}
}