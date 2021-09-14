<?php

class m191004_100000_add_ethnics extends \OEMigration
{
    public function up()
    {
        $this->addOEColumn('ethnic_group', 'id_assignment', 'INT UNSIGNED NULL', true);
        $this->addOEColumn('ethnic_group', 'describe_needs', 'tinyint(1) NOT NULL DEFAULT 0', true);

        $this->insert('ethnic_group', [
            'name' => 'English/Northern Irish/Scottish/Welsh/British',
            'id_assignment' => 1,
            'display_order' => 10,
            'code' => 'I'

        ]);

        $this->insert('ethnic_group', [
            'name' => 'Irish',
            'id_assignment' => 2,
            'display_order' => 20,
            'code' => 'O'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Any other White background, describe below',
            'id_assignment' => 3,
            'describe_needs' => 1,
            'display_order' => 30,
            'code' => 'Q'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'White and Black Caribbean',
            'id_assignment' => 4,
            'display_order' => 40,
            'code' => 'S'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'White and Black African',
            'id_assignment' => 5,
            'display_order' => 50,
            'code' => 'T'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'White and Asian',
            'id_assignment' => 6,
            'display_order' => 60,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Any other Mixed/Multiple ethnic background, describe below',
            'id_assignment' => 7,
            'describe_needs' => 1,
            'display_order' => 70,
            'code' => 'X'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Indian',
            'id_assignment' => 8,
            'display_order' => 80,
            'code' => 'Y'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Pakistani',
            'id_assignment' => 9,
            'display_order' => 90,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Bangladeshi',
            'id_assignment' => 10,
            'display_order' => 100,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Any other Asian background, describe below',
            'id_assignment' => 11,
            'describe_needs' => 1,
            'display_order' => 110,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'African',
            'id_assignment' => 13,
            'display_order' => 120,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'id_assignment' => 12,
            'name' => 'Caribbean',
            'display_order' => 130,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Any other Black/African/Caribbean background, describe below',
            'id_assignment' => 14,
            'describe_needs' => 1,
            'display_order' => 140,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Chinese',
            'display_order' => 150,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Any other Chinese background, describe below',
            'id_assignment' => 15,
            'describe_needs' => 1,
            'display_order' => 160,
            'code' => 'Z'
        ]);

        $this->insert('ethnic_group', [
            'name' => 'Other, describe below',
            'describe_needs' => 1,
            'display_order' => 170,
            'code' => 'Z'
        ]);
    }

    public function down()
    {
        $this->dropOEColumn('ethnic_group', 'id_assignment', true);
        $this->dropOEColumn('ethnic_group', 'describe_needs', true);
    }
}
