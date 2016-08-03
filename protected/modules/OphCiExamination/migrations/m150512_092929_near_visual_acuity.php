<?php

class m150512_092929_near_visual_acuity extends OEMigration
{
    protected $baseValues = array(
        '110',
        '105',
        '100',
        '95',
        '90',
        '85',
        '80',
        '75',
        '70',
        '65',
        '60',
        '55',
        '50',
    );
    protected $nearUnits = array(
        'Reduced LogMAR' => array(
            '0.0',
            '0.1',
            '0.2',
            '0.3',
            '0.4',
            '0.5',
            '0.6',
            '0.7',
            '0.8',
            '0.9',
            '1.0',
            '1.1',
            '1.2',
        ),
        'Reduced Snellen' => array(
            '6/6',
            '6/7.5',
            '6/9.5',
            '6/11',
            '6/15',
            '6/18',
            '6/23',
            '6/30',
            '6/37',
            '6/47',
            '6/60',
            '6/75',
            '6/95',
        ),
        'N Scale' => array(
            'N3.2',
            'N4',
            'N5',
            'N6.3',
            'N8',
            'N10',
            'N12.5',
            'N16',
            'N20',
            'N25',
            'N32',
            'N40',
            'N50',
        ),
        'Jaeger (Approx)' => array(
            'J1',
            'J1-J2',
            'J1-J3',
            'J1-J5',
            'J3-J6',
            'J4-J7',
            'J5-J9',
            'J8-J12',
            'J9-J12',
            'J10-J15',
            'J15-J18',
        ),
    );

    public function up()
    {

        //These tables are versioned seperately because the ID needs to be different from what PK creates for the FK to work
        $this->createOETable('et_ophciexamination_nearvisualacuity', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'event_id' => 'int(10) unsigned NOT NULL',
            'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
            'unit_id' => 'int(10) unsigned not null',
            'left_unable_to_assess' => 'tinyint(1) unsigned not null',
            'right_unable_to_assess' => 'tinyint(1) unsigned not null',
            'left_eye_missing' => 'tinyint(1) unsigned not null',
            'right_eye_missing' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`id`)',
            'CONSTRAINT `et_ophciexamination_nearvisualacuity_unit_fk` FOREIGN KEY (`unit_id`) REFERENCES `ophciexamination_visual_acuity_unit` (`id`)',
            'CONSTRAINT `et_ophciexamination_nearvisualacuity_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
            'CONSTRAINT `et_ophciexamination_nearvisualacuity_e_id_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
        ), false);

        $this->versionExistingTable('et_ophciexamination_nearvisualacuity');

        $this->createOETable('ophciexamination_nearvisualacuity_reading', array(
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
            'element_id' => 'int(10) unsigned NOT NULL',
            'value' => 'int(10) unsigned NOT NULL',
            'method_id' => 'int(10) unsigned not null',
            'side' => 'tinyint(1) unsigned not null',
            'PRIMARY KEY (`id`)',
            'CONSTRAINT `ophciexamination_nearvisualacuity_reading_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophciexamination_nearvisualacuity` (`id`)',
            'CONSTRAINT `ophciexamination_nearvisualacuity_reading_method_id_fk` FOREIGN KEY (`method_id`) REFERENCES `ophciexamination_visualacuity_method` (`id`)',
        ), false);

        $this->versionExistingTable('ophciexamination_nearvisualacuity_reading');

        $visualFunction = $this->getDbConnection()->createCommand('select id from element_type where `name` = "Visual Function"')->queryRow();
        $eventType = $this->getDbConnection()->createCommand('select id from event_type where `name` = "Examination"')->queryRow();

        if ($visualFunction && $eventType) {
            $nearVisualElement = array(
                'name' => 'Near Visual Acuity',
                'class_name' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity',
                'event_type_id' => $eventType['id'],
                'display_order' => 20,
                'default' => 1,
                'parent_element_type_id' => $visualFunction['id'],
                'required' => 0,
            );
            $this->insert('element_type', $nearVisualElement);
            $visualRow = $this->getDbConnection()->createCommand('select id from element_type where `name` = "Near Visual Acuity"')->queryRow();
        }

        $this->addColumn('ophciexamination_visual_acuity_unit', 'is_near', 'TINYINT(1) NOT NULL DEFAULT 0');
        $this->alterColumn('ophciexamination_visual_acuity_unit', 'name', 'VARCHAR(100) NOT NULL');

        $defaultSet = false;
        foreach ($this->nearUnits as $unit => $values) {
            $this->insert('ophciexamination_visual_acuity_unit', array(
                'name' => $unit,
                'is_near' => 1,
                'active' => 1,
            ));
            $unitRow = $this->getDbConnection()->createCommand('select id from ophciexamination_visual_acuity_unit where `name` = "'.$unit.'"')->queryRow();
            if ($unitRow) {
                if (!$defaultSet) {
                    $this->insert('setting_metadata', array(
                        'element_type_id' => $visualRow['id'],
                        'field_type_id' => 2,
                        'key' => 'unit_id',
                        'name' => 'Units',
                        'default_value' => $unitRow['id'],
                    ));
                    $defaultSet = true;
                }
                foreach ($values as $key => $value) {
                    $this->insert('ophciexamination_visual_acuity_unit_value', array(
                        'unit_id' => $unitRow['id'],
                        'value' => $value,
                        'base_value' => $this->baseValues[$key],
                        'selectable' => '1',
                    ));
                }
            }
        }
    }

    public function down()
    {
        $this->dropOETable('ophciexamination_nearvisualacuity_reading', true);
        $this->dropOETable('et_ophciexamination_nearvisualacuity', true);
        $this->delete('element_type', 'name = "Near Visual Acuity"');
        $this->dropColumn('ophciexamination_visual_acuity_unit', 'is_near');

        $newUnitIds = $this->getDbConnection()->createCommand('select GROUP_CONCAT(id) as new_ids from ophciexamination_visual_acuity_unit where `name` in ("'.implode('","', array_keys($this->nearUnits)).'")')->queryRow();
        if ($newUnitIds && $newUnitIds['new_ids']) {
            $this->delete('ophciexamination_visual_acuity_unit_value', 'unit_id in ('.$newUnitIds['new_ids'].')');
            $this->delete('ophciexamination_visual_acuity_unit', 'id in ('.$newUnitIds['new_ids'].')');
        }
    }
}
