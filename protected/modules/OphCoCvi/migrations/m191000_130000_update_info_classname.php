<?php

class m191000_130000_update_info_classname extends OEMigration
{
    public function up()
    {
        $this->execute("DELETE FROM element_type WHERE class_name LIKE '%Element_OphCoCvi_EventInfo%' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");
        $this->execute("DELETE FROM element_type WHERE class_name LIKE '%Element_OphCoCvi_Demographics%' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");
        
        $this->createElementType("OphCoCvi", "Event Info", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_EventInfo_V1',
            'default' => true,
            'required' => true,
            'display_order' => 1,
        ]);
        
        $this->createElementType("OphCoCvi", "Demographics", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Demographics_V1',
            'default' => true,
            'required' => true,
            'display_order' => 10,
        ]);
        
    }

    public function down()
    {
        $this->execute("DELETE FROM element_type WHERE class_name LIKE '%Element_OphCoCvi_EventInfo_V1%' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");
        $this->execute("DELETE FROM element_type WHERE class_name LIKE '%Element_OphCoCvi_Demographics_V1%' AND event_type_id = (SELECT id FROM event_type WHERE event_type.class_name = 'OphCoCvi')");
        
        $this->createElementType("OphCoCvi", "Event Info", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_EventInfo',
            'default' => true,
            'required' => true,
            'display_order' => 1,
        ]);
        
         $this->createElementType("OphCoCvi", "Demographics", [
            'class_name' => 'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Demographics',
            'default' => true,
            'required' => true,
            'display_order' => 1,
        ]);
    }
}