<?php

class m180626_061532_remove_element_parenting extends OEMigration
{
    public function safeUp()
    {
        // Observations and Pupillary Abnormalities have the same display order, which causes some sorting issues
        // Bump up the PA display order now to prevent any issues in the rest of the patch
        $this->update('element_type', array('display_order' => 12), 'class_name = :class_name',
            array(':class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_VisualFunction'));

        $event_type_ids = $this->dbConnection->createCommand()->select('id')->from('event_type')->queryColumn();

        foreach ($event_type_ids as $event_type_id) {

            // Order elements with the complex "by parent display orde,r then by child display order with parents at teh start of their groups"
            $elements = $this->dbConnection->createCommand()->select('*')->from('element_type')->where('event_type_id = :event_type_id',
                array(':event_type_id' => $event_type_id))->order(
                'COALESCE((SELECT parent.display_order FROM element_type parent WHERE parent.id = element_type.parent_element_type_id), element_type.display_order), 
                 CASE WHEN element_type.parent_element_type_id IS NULL THEN -1 ELSE element_type.display_order END'
            )->queryAll();

            $display_order = 10;

            // Then replace with simpler display_order
            foreach ($elements as $element) {
                $this->update('element_type', array('display_order' => $display_order), 'id = :id',
                    array(':id' => $element['id']));
                $display_order += 10;
            }
        }

        // Parent elements are no longer a thing, but the sidebar still needs element groups, so we'll make a new table here
        $this->createOETable('element_group', array(
            'id' => 'pk',
            'name' => 'varchar(100)',
            'display_order' => 'int(10) unsigned',
            'event_type_id' => 'int(10) unsigned',
        ), true);
        $this->addForeignKey('element_group_event_type_id_fk', 'element_group', 'event_type_id', 'event_type', 'id');

        $this->addColumn('element_type', 'element_group_id', 'int(11)');
        $this->addColumn('element_type_version', 'element_group_id', 'int(11)');

        $this->addForeignKey('element_type_element_group_id_fk', 'element_type', 'element_group_id', 'element_group',
            'id');

        foreach ($event_type_ids as $event_type_id) {

            $event_name = $this->dbConnection->createCommand()
                ->select('name')
                ->from('event_type')
                ->where('id = :id', array(':id' => $event_type_id))
                ->queryScalar();

            $parent_elements = $this->dbConnection->createCommand()
                ->select('*')
                ->from('element_type')
                ->where('parent_element_type_id IS NULL AND event_type_id = :event_type_id',
                    array(':event_type_id' => $event_type_id))
                ->order('display_order')
                ->queryAll();

            $display_order = 10;

            // Populate the group table using parents elements in the original table
            foreach ($parent_elements as $parent_element) {
                $this->insert('element_group', array(
                    'name' => $parent_element['group_title'] ?: $parent_element['name'],
                    'event_type_id' => $event_type_id,
                    'display_order' => $display_order,
                ));
                $element_group_id = $this->dbConnection->createCommand()->select('MAX(id)')->from('element_group')->queryScalar();
                $display_order += 10;

                $this->update('element_type', array('element_group_id' => $element_group_id),
                    'id = :id OR parent_element_type_id = :parent_element_type_id',
                    array(':id' => $parent_element['id'], ':parent_element_type_id' => $parent_element['id']));
            }

            // If there exists an element in the event that doesn't have an element group
            if ($this->dbConnection->createCommand()
                ->from('element_type')
                ->where('element_group_id IS NULL AND event_type_id = :event_type_id',
                    array(':event_type_id' => $event_type_id))
                ->queryAll()) {
                // Then make a new one
                $this->insert('element_group', array(
                    'event_type_id' => $event_type_id,
                    'name' => $event_name,
                    'display_order' => $display_order,
                ));

                $element_group_id = $this->dbConnection->createCommand()->select('MAX(id)')->from('element_group')->queryScalar();

                $this->update('element_type', array('element_group_id' => $element_group_id),
                    'event_type_id = :event_type_id AND element_group_id IS NULL AND parent_element_type_id IS NULL',
                    array(':event_type_id' => $event_type_id));
            }
        }

        // Group tiles are now located in the element_group table, and the column can now bw removed
        // Check that group_title exists
        $table = $this->dbConnection->schema->getTable('element_type');
        if(!isset($table->columns['group_title'])) {
            //Assume all these things are extant if group title is
            $this->dropColumn('element_type', 'group_title');
            $this->dropColumn('element_type_version', 'group_title');

            $this->dropForeignKey('element_type_parent_et_fk', 'element_type');

            $this->dropColumn('element_type', 'parent_element_type_id');
            $this->dropColumn('element_type_version', 'parent_element_type_id');
        }
    }

    public function safeDown()
    {
        echo "m180626_061532_remove_element_parenting does not support migration down.\n";

        return false;
    }
}