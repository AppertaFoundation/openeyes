<?php

class m170819_201700_add_dispense_condition_and_location extends OEMigration
{
    public function up()
    {
        $conditions = array('Hospital to supply', 'Hospital to supply and GP to continue','GP to supply','Patient self-supply');
        $locations = array('N/A','Pharmacy','TTO Pre-Pack','Patient Own Drugs','Ward Fridge','Home','FP 10 HP','Already On');
        $conditions_setup = array(
            'Hospital to supply'=>array('Pharmacy','TTO Pre-Pack','Ward Fridge'),
            'Hospital to supply and '.\SettingMetadata::model()->getSetting('gp_label').' to continue'=>array('Pharmacy','TTO Pre-Pack','Ward Fridge'),
            'GP to supply'=>array('N/A'),
            'Patient self-supply'=>array('Patient Own Drugs','Home','Already On')
            );
        $this->createOETable(
            'ophdrprescription_dispense_condition',
            array(
                                    'id' => 'pk',
                                    'name' => 'varchar(255) not null',
                                    'display_order' => 'integer not null',
                                    'active' => 'boolean not null default true'
                                ),
            true
        );

        $this->createOETable(
            'ophdrprescription_dispense_location',
            array(
                                    'id' => 'pk',
                                    'name' => 'varchar(255) not null',
                                    'display_order' => 'integer not null',
                                    'active' => 'boolean not null default true'
            ),
            true
        );

        $this->createOETable(
            'ophdrprescription_dispense_condition_assignment',
            array(
                                'id' => 'pk',
                                'dispense_condition_id' => 'int(11)',
                                'dispense_location_id' => 'int(11)'
            ),
            true
        );

        $this->addForeignKey(
            'fk_ophdrprescription_dispense_condition_assignment_condition_id',
            'ophdrprescription_dispense_condition_assignment',
            'dispense_condition_id',
            'ophdrprescription_dispense_condition',
            'id'
        );

        $this->addForeignKey(
            'fk_ophdrprescription_dispense_condition_assignment_location_id',
            'ophdrprescription_dispense_condition_assignment',
            'dispense_location_id',
            'ophdrprescription_dispense_location',
            'id'
        );

        $do_cond = 1;
        foreach ($conditions as $condition) {
            $this->insert('ophdrprescription_dispense_condition', array('name'=>$condition, 'display_order'=>$do_cond));
            $do_cond++;
        }

        $do_loc = 1;
        foreach ($locations as $location) {
            $this->insert('ophdrprescription_dispense_location', array('name'=>$location, 'display_order'=>$do_loc));
            $do_loc++;
        }

        foreach ($conditions_setup as $cond=>$loc) {
            $condition_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('ophdrprescription_dispense_condition')
                ->where("name='".$cond."'")->queryScalar();
            if ($condition_id) {
                foreach ($loc as $l) {
                    $location_id = $this->dbConnection->createCommand()
                        ->select('id')
                        ->from('ophdrprescription_dispense_location')
                        ->where("name='".$l."'")->queryScalar();
                    if ($location_id) {
                        $this->insert(
                            'ophdrprescription_dispense_condition_assignment',
                            array(
                                'dispense_condition_id' => $condition_id,
                                'dispense_location_id' => $location_id
                            )
                        );
                    }
                }
            }
        }

        $this->addColumn('ophdrprescription_item', 'dispense_condition_id', 'int(11)');
        $this->addColumn('ophdrprescription_item_version', 'dispense_condition_id', 'int(11)');
        $this->addColumn('ophdrprescription_item', 'dispense_location_id', 'int(11)');
        $this->addColumn('ophdrprescription_item_version', 'dispense_location_id', 'int(11)');

        $this->addForeignKey(
            'fk_ophdrprescription_item_dispense_condition_id',
            'ophdrprescription_item',
            'dispense_condition_id',
            'ophdrprescription_dispense_condition',
            'id'
        );

        $this->addForeignKey(
            'fk_ophdrprescription_item_dispense_location_id',
            'ophdrprescription_item',
            'dispense_location_id',
            'ophdrprescription_dispense_location',
            'id'
        );

        echo "Migrating existing Prescription events\n";
        /*
            Migrations:
              Where GP to Continue = True, migrate to: 'Hospital to supply and GP to Continue' + 'N/A'
              All other existing items (i.e., where GP to continue is false) migrate to: 'Hospital to supply' + 'N/A'
         */
        $continue_gp_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophdrprescription_dispense_condition')
            ->where("name='Hospital to supply and GP to continue'")->queryScalar();

        $hospital_supply_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophdrprescription_dispense_condition')
            ->where("name='Hospital to supply'")->queryScalar();

        $location_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophdrprescription_dispense_location')
            ->where("name='N/A'")->queryScalar();

        $this->update(
            'ophdrprescription_item',
            array('dispense_condition_id'=>$continue_gp_id, 'dispense_location_id'=>$location_id),
            'continue_by_gp = 1'
        );

        $this->update(
            'ophdrprescription_item',
            array('dispense_condition_id'=>$hospital_supply_id, 'dispense_location_id'=>$location_id),
            'continue_by_gp = 0'
        );

        $this->dropColumn('ophdrprescription_item', 'continue_by_gp');
        $this->dropColumn('ophdrprescription_item_version', 'continue_by_gp');
    }

    public function down()
    {
        $this->dropForeignKey('fk_ophdrprescription_dispense_condition_assignment_condition_id', 'ophdrprescription_dispense_condition_assignment');
        $this->dropForeignKey('fk_ophdrprescription_dispense_condition_assignment_location_id', 'ophdrprescription_dispense_condition_assignment');
        $this->dropForeignKey('fk_ophdrprescription_item_dispense_condition_id', 'ophdrprescription_item');
        $this->dropForeignKey('fk_ophdrprescription_item_dispense_location_id', 'ophdrprescription_item');

        $this->addColumn('ophdrprescription_item', 'continue_by_gp', 'boolean not null default 0');
        $this->addColumn('ophdrprescription_item_version', 'continue_by_gp', 'boolean not null default 0');

        $condition_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('ophdrprescription_dispense_condition')
            ->where("name='Hospital to supply and GP to continue'")->queryScalar();

        $this->update('ophdrprescription_item', array('continue_by_gp'=>1), "dispense_condition_id = '".$condition_id."'");

        $this->dropColumn('ophdrprescription_item', 'dispense_condition_id');
        $this->dropColumn('ophdrprescription_item_version', 'dispense_condition_id');
        $this->dropColumn('ophdrprescription_item', 'dispense_location_id');
        $this->dropColumn('ophdrprescription_item_version', 'dispense_location_id');

        $this->dropOETable('ophdrprescription_dispense_condition_assignment', true);
        $this->dropOETable('ophdrprescription_dispense_location', true);
        $this->dropOETable('ophdrprescription_dispense_condition', true);


    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
