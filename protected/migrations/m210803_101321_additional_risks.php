<?php

class m210803_101321_additional_risks extends OEMigration
{
    private $data = array(
        ["display_order" => 1, "name" => "Latex allergy - first on list"],
        ["display_order" => 2, "name" => "Iodine sensitivity post injection lubricants advised"],
        ["display_order" => 3, "name" => "Iodine allergy chlorhexidine gluconate 0.02% advised"],
        ["display_order" => 4, "name" => "Warfarin INR towards lower limit of range (eg towards 2 if INR range 2-3)"],
        ["display_order" => 5, "name" => "Stroke, Myocardial Infarction, Transient Ischaemic Attacks (within 3 months)"],
        ["display_order" => 6, "name" => "Dementia record Mental Capacity Assessment at each injection visit"],
        ["display_order" => 7, "name" => "Precautions against pregnancy"],
        ["display_order" => 8, "name" => "Care home/Supported Living eye care plan (eg no eye rubbing, blepharitis prevention)"]
    );

    public function safeUp()
    {
        $institutions = \Institution::model()->findAll();

        foreach ($institutions as $institution) {
            $institution_id = $institution->id;
            array_walk($this->data, function (&$item, $i, $institution_id) {
                $item['institution_id'] = $institution_id;
            }, $institution_id);
            $this->insertMultiple('ophtrconsent_additional_risk', $this->data);
        }
    }

    public function safeDown()
    {
        foreach ($this->data as $risk) {
            $this->delete('ophtrconsent_additional_risk', 'name=:name', array(':name'=>$risk['name']));
        }
    }
}
