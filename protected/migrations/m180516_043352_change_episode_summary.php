<?php
class m180516_043352_change_episode_summary extends OEMigration
{
    public function safeUp()
    {
        // Glaucoma summary change
        $glaucoma_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Glaucoma"')->queryRow();
        $this->insert('episode_summary_item', array('event_type_id' => 27, 'name' => 'Medication'));
        $item_id =  $this->getDbConnection()->createCommand('select id from episode_summary_item where name ="Medication"')->queryRow();
        $this->insert('episode_summary', array('item_id'=>$item_id['id'], 'subspecialty_id'=>$glaucoma_id['id'], 'display_order'=>0));
        $this->delete('episode_summary', '`item_id` = 1 and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('episode_summary', '`item_id` = 2 and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('episode_summary', '`item_id` = 5 and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('episode_summary', '`item_id` = 6 and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('episode_summary', '`item_id` = 7 and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('episode_summary', '`item_id` = 9 and `subspecialty_id`='.$glaucoma_id['id']);
        $this->update('episode_summary', array('display_order'=>1), '`item_id` = 4 and `subspecialty_id`='.$glaucoma_id['id']);
        $this->update('episode_summary', array('display_order'=>2), '`item_id` = 8 and `subspecialty_id`='.$glaucoma_id['id']);
        //Cataract summary change
        $cataract_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Cataract"')->queryRow();
        $this->delete('episode_summary', '`item_id` = 4 and `subspecialty_id`='.$cataract_id['id']);
        $this->delete('episode_summary', '`item_id` = 5 and `subspecialty_id`='.$cataract_id['id']);
    }
    public function safeDown()
    {
        //Glaucoma summary change back
        $glaucoma_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Glaucoma"')->queryRow();
        $this->insert('episode_summary', array('item_id'=>1, 'subspecialty_id'=> $glaucoma_id['id'], 'display_order'=>3 ));
        $this->insert('episode_summary', array('item_id'=>2, 'subspecialty_id'=> $glaucoma_id['id'], 'display_order'=>2 ));
        $this->insert('episode_summary', array('item_id'=>5, 'subspecialty_id'=> $glaucoma_id['id'], 'display_order'=>0 ));
        $this->insert('episode_summary', array('item_id'=>6, 'subspecialty_id'=> $glaucoma_id['id'], 'display_order'=>1 ));
        $this->insert('episode_summary', array('item_id'=>7, 'subspecialty_id'=> $glaucoma_id['id'], 'display_order'=>4 ));
        $this->insert('episode_summary', array('item_id'=>9, 'subspecialty_id'=> $glaucoma_id['id'], 'display_order'=>7 ));
        $this->update('episode_summary', array('display_order'=>6), '`item_id` = 4 and `subspecialty_id` = '.$glaucoma_id['id']);
        $this->update('episode_summary', array('display_order'=>5), '`item_id` = 8 and `subspecialty_id` = '.$glaucoma_id['id']);
        $item_id =  $this->getDbConnection()->createCommand('select id from episode_summary_item where name ="Medication"')->queryRow();
        $this->delete('episode_summary', '`item_id`='.$item_id["id"].' and `subspecialty_id`='.$glaucoma_id['id']);
        $this->delete('episode_summary_item', '`name`="Medication"');
        //Cataract summary change back
        $cataract_id = $this->getDbConnection()->createCommand('select id from subspecialty where name ="Cataract"')->queryRow();
        $this->insert('episode_summary', array('item_id'=>4, 'subspecialty_id'=> $cataract_id['id'], 'display_order'=>2 ));
        $this->insert('episode_summary', array('item_id'=>5, 'subspecialty_id'=> $cataract_id['id'], 'display_order'=>0 ));
    }
}