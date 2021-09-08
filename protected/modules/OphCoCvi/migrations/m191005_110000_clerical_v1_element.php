<?php

class m191005_110000_clerical_v1_element extends OEMigration
{
    public function up()
    {
        $this->execute("DELETE FROM element_type WHERE class_name LIKE '%Element_OphCoCvi_ClericalInfo%' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");

        $this->createElementType("OphCoCvi", "Clerical Info", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ClericalInfo_V1',
            'default' => true,
            'required' => true,
            'display_order' => 40,
        ]);
    }

    public function down()
    {
        $this->execute("DELETE FROM element_type WHERE class_name LIKE '%Element_OphCoCvi_ClericalInfo_V1%' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");

         $this->createElementType("OphCoCvi", "Clerical Info", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ClericalInfo',
            'default' => true,
            'required' => true,
            'display_order' => 1,
         ]);
    }
}
