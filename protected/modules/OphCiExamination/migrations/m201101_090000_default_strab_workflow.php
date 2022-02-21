<?php

use OEModule\OphCiExamination\models\BirthHistory;
use OEModule\OphCiExamination\models\ContrastSensitivity;
use OEModule\OphCiExamination\models\ConvergenceAccommodation;
use OEModule\OphCiExamination\models\CorrectionGiven;
use OEModule\OphCiExamination\models\CoverAndPrismCover;
use OEModule\OphCiExamination\models\PrismReflex;
use OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision;
use OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;
use OEModule\OphCiExamination\models\Element_OphCiExamination_History;
use OEModule\OphCiExamination\models\HeadPosture;
use OEModule\OphCiExamination\models\NinePositions;
use OEModule\OphCiExamination\models\PostOpDiplopiaRisk;
use OEModule\OphCiExamination\models\PrismFusionRange;
use OEModule\OphCiExamination\models\PupillaryAbnormalities;
use OEModule\OphCiExamination\models\RedReflex;
use OEModule\OphCiExamination\models\Retinoscopy;
use OEModule\OphCiExamination\models\SensoryFunction;
use OEModule\OphCiExamination\models\StereoAcuity;
use OEModule\OphCiExamination\models\StrabismusManagement;
use OEModule\OphCiExamination\models\Synoptophore;

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

class m201101_090000_default_strab_workflow extends OEMigration
{

    protected $strabismus_elements = [
        'OEModule\OphCiExamination\models\Element_OphCiExamination_History',
        'OEModule\OphCiExamination\models\BirthHistory',
        'OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity',
        'OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity',
        'OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision',
        'OEModule\OphCiExamination\models\ContrastSensitivity',
        'OEModule\OphCiExamination\models\HeadPosture',
        'OEModule\OphCiExamination\models\CoverAndPrismCover',
        'OEModule\OphCiExamination\models\NinePositions',
        'OEModule\OphCiExamination\models\ConvergenceAccommodation',
        'OEModule\OphCiExamination\models\SensoryFunction',
        'OEModule\OphCiExamination\models\PrismReflex',
        'OEModule\OphCiExamination\models\PrismFusionRange',
        'OEModule\OphCiExamination\models\StereoAcuity',
        'OEModule\OphCiExamination\models\Synoptophore',
        'OEModule\OphCiExamination\models\PostOpDiplopiaRisk',
        'OEModule\OphCiExamination\models\RedReflex',
        'OEModule\OphCiExamination\models\Element_OphCiExamination_Refraction',
        'OEModule\OphCiExamination\models\Retinoscopy',
        'OEModule\OphCiExamination\models\CorrectionGiven',
        'OEModule\OphCiExamination\models\StrabismusManagement'
    ];

    protected $workflow_name = 'Strabismus workflow';

    public function safeUp()
    {
        $subspecialty_ids = [
            $this->getIdOfSubspecialtyByName('Strabismus'),
            $this->getIdOfSubspecialtyByName('Paediatrics')
        ];

        $this->insert('ophciexamination_workflow', ['name' => $this->workflow_name]);
        $workflow_id = $this->getDbConnection()->getLastInsertID();

        foreach ($subspecialty_ids as $subspecialty_id) {
            $this->insert('ophciexamination_workflow_rule', [
                'workflow_id' => $workflow_id,
                'subspecialty_id' => $subspecialty_id
            ]);
        }

        $this->insert('ophciexamination_element_set', [
            'name' => 'All elements',
            'workflow_id' => $workflow_id
        ]);

        $set_id = $this->getDbConnection()->getLastInsertID();

        foreach ($this->strabismus_elements as $i => $element_cls) {
            $element_id = $this->getIdOfElementTypeByClassName($element_cls);
            $this->insert('ophciexamination_element_set_item', [
                'set_id' => $set_id,
                'element_type_id' => $element_id,
                'display_order' => $i+1
            ]);
        }

        return true;
    }

    public function safeDown()
    {
        $workflow_id = $this->getDbConnection()
            ->createCommand()
            ->select('id')
            ->from('ophciexamination_workflow')
            ->where('name = :name', [":name" => $this->workflow_name])
            ->queryScalar();

        // delete element entries from sets
        $set_query = $this->getDbConnection()
            ->createCommand()
            ->select('id')
            ->from('ophciexamination_element_set')
            ->where('workflow_id = :workflow_id', [':workflow_id' => $workflow_id]);

        foreach ($set_query->queryAll() as $row) {
            $this->delete(
                'ophciexamination_element_set_item',
                'set_id = :set_id',
                [':set_id' => $row['id']]
            );
            $this->delete(
                'ophciexamination_event_elementset_assignment',
                'step_id = :set_id',
                [':set_id' => $row['id']]
            );
        }

        // delete sets
        $this->delete(
            'ophciexamination_element_set',
            'workflow_id = :workflow_id',
            [':workflow_id' => $workflow_id]
        );

        // delete rules
        $this->delete(
            'ophciexamination_workflow_rule',
            'workflow_id = :workflow_id',
            [':workflow_id' => $workflow_id]
        );

        // delete workflow
        $this->delete(
            'ophciexamination_workflow',
            'id = :id',
            [':id' => $workflow_id]
        );

        return true;
    }
}
