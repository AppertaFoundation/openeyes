<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m200428_075833_create_synoptophore extends OEMigration
{
    protected const GROUP_NAME = 'Orthoptic Testing';
    protected const ELEMENT_CLS_NAME = 'OEModule\OphCiExamination\models\Synoptophore';
    public function safeUp()
    {

        // if an old version of synoptaphore exists (i.e, from MEH), archive it first
        $exists = $this->dbConnection->createCommand("SELECT Count(*)
                                                        FROM information_schema.tables 
                                                        WHERE table_schema = DATABASE()
                                                            AND table_type = 'BASE TABLE'
                                                            AND table_name = 'et_ophciexamination_synoptophore';'")->queryScalar();

        if ($exists >= 1) {
            // remove foreign keys
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP FOREIGN KEY `fk_et_ophciexamination_synoptophore_event`")->execute();
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP FOREIGN KEY `fk_et_ophciexamination_synoptophore_pf`")->execute();
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP FOREIGN KEY `et_ophciexamination_synoptophore_cui_fk`")->execute();
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP FOREIGN KEY `et_ophciexamination_synoptophore_lmui_fk`")->execute();

            // remove / rename indexes
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP INDEX `et_ophciexamination_synoptophore_cui_fk`")->execute();
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP INDEX `et_ophciexamination_synoptophore_lmui_fk`")->execute();
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP INDEX `fk_et_ophciexamination_synoptophore_event`")->execute();
            $this->dbConnection->createCommand("ALTER TABLE `et_ophciexamination_synoptophore` DROP INDEX `fk_et_ophciexamination_synoptophore_pf`")->execute();

            $this->dbConnection->createCommand("RENAME TABLE `et_ophciexamination_synoptophore` TO `archive_et_ophciexamination_synoptophore`")->execute();
            $this->dbConnection->createCommand("RENAME TABLE `et_ophciexamination_synoptophore_version` TO `archive_et_ophciexamination_synoptophore_version`")->execute();
        }

        $this->createElementType('OphCiExamination', 'Synoptophore', [
            'class_name' => self::ELEMENT_CLS_NAME,
            'display_order' => 405,
            'group_name' => self::GROUP_NAME
        ]);

        $this->createOETable(
            'et_ophciexamination_synoptophore',
            [
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 3',
                'angle_from_primary' => 'smallint NOT NULL',
                'comments' => 'text',
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_synoptophore_ev_fk',
            'et_ophciexamination_synoptophore',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable(
            'ophciexamination_synoptophore_direction',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
            true
        );

        $this->createOETable(
            'ophciexamination_synoptophore_deviation',
            [
                'id' => 'pk',
                'name' => 'varchar(31)',
                'abbreviation' => 'varchar(7)',
                'display_order' => 'tinyint default 1 not null',
                'active' => 'boolean default true',
            ],
            true
        );

        $this->initialiseData(dirname(__FILE__));

        $this->createOETable(
            'ophciexamination_synoptophore_readingforgaze',
            [
                'id' => 'pk',
                'element_id' => 'int(11)',
                'gaze_type' => 'varchar(31)',
                'horizontal_angle' => 'int(11)',
                'vertical_power' => 'int(11)',
                'direction_id' => 'int(11)',
                'torsion' => 'int(11)',
                'deviation_id' => 'int(11)',
                'eye_id' => 'int(10) unsigned NOT NULL'
            ],
            true
        );

        $this->addForeignKey(
            'et_ophciexamination_synoptophore_readingforgaze_el_fk',
            'ophciexamination_synoptophore_readingforgaze',
            'element_id',
            'et_ophciexamination_synoptophore',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_synoptophore_readingforgaze_di_fk',
            'ophciexamination_synoptophore_readingforgaze',
            'direction_id',
            'ophciexamination_synoptophore_direction',
            'id'
        );

        $this->addForeignKey(
            'et_ophciexamination_synoptophore_readingforgaze_de_fk',
            'ophciexamination_synoptophore_readingforgaze',
            'deviation_id',
            'ophciexamination_synoptophore_deviation',
            'id'
        );
        $examination_id = $this->getIdOfEventTypeByClassName('OphCiExamination');
        $this->insert('index_search', [
            'event_type_id' => $examination_id,
            'primary_term' => 'Synoptophore',
            'open_element_class_name' => self::ELEMENT_CLS_NAME,
        ]);
    }

    public function safeDown()
    {
        $this->delete('index_search', 'open_element_class_name = ? ', [self::ELEMENT_CLS_NAME]);

        $this->dropOETable('ophciexamination_synoptophore_readingforgaze', true);

        $this->dropOETable('ophciexamination_synoptophore_deviation', true);

        $this->dropOETable('ophciexamination_synoptophore_direction', true);

        $this->dropOETable('et_ophciexamination_synoptophore', true);

        $this->deleteElementType('OphCiExamination', self::ELEMENT_CLS_NAME);
    }
}
