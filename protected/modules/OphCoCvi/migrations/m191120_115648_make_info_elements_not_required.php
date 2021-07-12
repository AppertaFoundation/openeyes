<?php

class m191120_115648_make_info_elements_not_required extends CDbMigration
{
    private $class_names = "'OEModule\\\OphCoCvi\\models\\', 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ClinicalInfo_V1'";

	public function up()
	{
	    $this->execute("UPDATE element_type SET required = 0 WHERE
                            (
                            class_name LIKE '%Element_OphCoCvi_ClericalInfo_V1' OR 
                            class_name LIKE '%Element_OphCoCvi_ClinicalInfo_V1'
                            )");
	}

	public function down()
	{
        $this->execute("UPDATE element_type SET required = 1 WHERE
                            (
                            class_name LIKE '%Element_OphCoCvi_ClericalInfo_V1' OR 
                            class_name LIKE '%Element_OphCoCvi_ClinicalInfo_V1'
                            )");
	}
}