<?php

class m221007_122400_expand_near_va_value_options extends OEMigration
{
    protected $old_unit_name = 'N Scale';
    protected $new_unit_name = 'N-Scale @40cm';

    protected $old_unit_values = [
        ['N50', 50],
        ['N40', 55],
        ['N32', 60],
        ['N25', 65],
        ['N20', 70],
        ['N16', 75],
        ['N12.5', 80],
        ['N10', 85],
        ['N8', 90],
        ['N6.3', 95],
        ['N5', 100],
        ['N4', 105],
        ['N3.2', 110]
    ];
    protected $new_unit_values = [
        ['N50', 49],
        ['N48', 50],
        ['N40', 55],
        ['N36', 56],
        ['N32', 60],
        ['N25', 65],
        ['N24', 66],
        ['N20', 70],
        ['N18', 71],
        ['N16', 75],
        ['N14', 77],
        ['N12.5', 80],
        ['N12', 81],
        ['N10', 85],
        ['N8', 90],
        ['N6.3', 94],
        ['N6', 95],
        ['N5', 100],
        ['N4', 105],
        ['N3.2', 109],
        ['N3', 110]
    ];


    public function safeUp()
    {
        $unit_id = $this->dbConnection->createCommand("SELECT id FROM ophciexamination_visual_acuity_unit WHERE name = '$this->old_unit_name';")->queryScalar();

        // Checking if the table has already been altered
        $existing_unit_values = array_map(function ($values) {
            return [$values['value'],$values['base_value']];
        }, $this->dbConnection->createCommand("
            SELECT value, base_value
            FROM ophciexamination_visual_acuity_unit_value
            WHERE unit_id = $unit_id
            ORDER BY base_value ASC;
        ")->queryAll());

        if ($existing_unit_values != $this->old_unit_values) {
            echo <<<ERR

It appears that the near visual acuity values already differ from those in the default install.
This migration should be skipped in this case and the changes applied manually after dealing with the changes.

To skip this migration run the following SQL query:
INSERT IGNORE INTO openeyes.tbl_migration (version,apply_time,last_modified_date,created_date) VALUES ('m221007_122400_expand_near_va_value_options',UNIX_TIMESTAMP(),NOW(),NOW());

To fix the visual acuity table to match the default install please follow the steps in the following link:
https://openeyes.atlassian.net/wiki/spaces/OP/pages/2245787649/OE-13585+-+Deviated+Near+Visual+Acuity+Values

ERR;
            return false;
        }

        // Adding unique unit-value constraint
        $this->createIndex('unique_unit_value', 'ophciexamination_visual_acuity_unit_value', ['unit_id', 'value'], true);

        $values_as_sql = array_map(function ($values) use ($unit_id) {
            return "($unit_id,'$values[0]',$values[1])";
        }, $this->new_unit_values);

        $this->execute("
            INSERT INTO ophciexamination_visual_acuity_unit_value (unit_id,value,base_value)
            VALUES " . implode(',', $values_as_sql) . " ON DUPLICATE KEY UPDATE base_value = VALUES(base_value);
        ");

        // Rename the N Scale unit
        $this->execute("UPDATE ophciexamination_visual_acuity_unit SET name = '$this->new_unit_name' WHERE name = '$this->old_unit_name'");
    }

    public function safeDown()
    {
        $unit_id = $this->dbConnection->createCommand("SELECT id FROM ophciexamination_visual_acuity_unit WHERE name = '$this->new_unit_name';")->queryScalar();

        // Checking if the table has already been altered
        $existing_unit_values = array_map(function ($values) {
            return [$values['value'],$values['base_value']];
        }, $this->dbConnection->createCommand("
            SELECT value, base_value
            FROM ophciexamination_visual_acuity_unit_value
            WHERE unit_id = $unit_id
            ORDER BY base_value ASC;
        ")->queryAll());

        if ($existing_unit_values != $this->new_unit_values) {
            echo <<<ERR

It appears that the near visual acuity values already differ from those in the default install.
This migration should be skipped in this case and the changes applied manually after dealing with the changes.

To skip undoing this migration run the following SQL query:
DELETE IGNORE FROM openeyes.tbl_migration WHERE version='m221007_122400_expand_near_va_value_options';

To fix the visual acuity table to match the default install please follow the steps in the following link:
https://openeyes.atlassian.net/wiki/spaces/OP/pages/2245787649/OE-13585+-+Deviated+Near+Visual+Acuity+Values

ERR;
            return false;
        }

        // Removing extra values that were added
        $values_as_sql = array_map(function ($values) {
            return "'$values[0]'";
        }, $this->old_unit_values);

        $this->execute("
            DELETE FROM ophciexamination_visual_acuity_unit_value
            WHERE value NOT IN (" . implode(',', $values_as_sql) . ")
            AND unit_id = $unit_id;
        ");

        $values_as_sql = array_map(function ($values) use ($unit_id) {
            return "($unit_id,'$values[0]',$values[1])";
        }, $this->old_unit_values);

        $this->execute("
            INSERT INTO ophciexamination_visual_acuity_unit_value (unit_id,value,base_value)
            VALUES " . implode(',', $values_as_sql) . " ON DUPLICATE KEY UPDATE base_value = VALUES(base_value);
        ");

        // Adding unique unit-value constraint
        $this->dropIndex('unique_unit_value', 'ophciexamination_visual_acuity_unit_value');

        // Rename the N Scale unit
        $this->execute("UPDATE ophciexamination_visual_acuity_unit SET name = '$this->old_unit_name' WHERE name = '$this->new_unit_name'");
    }
}
