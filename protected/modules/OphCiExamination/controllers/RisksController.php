<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\controllers;


use OEModule\OphCiExamination\models\HistoryRisks;
use OEModule\OphCiExamination\models\OphCiExaminationRisk;

class RisksController extends \BaseController
{
    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
    }

    /**
     * @param array $tag_ids
     * @return array
     *
     * @deprecated
     */
    protected function riskIdsForTagIds($tag_ids = array())
    {
        return array_map(
            function ($r) {
                return $r->id;
            },
            OphCiExaminationRisk::findForTagIds($tag_ids)
        );
    }

    /**
     * @param $tag_ids
     *
     * @deprecated
     */

    public function actionForTags($tag_ids)
    {
        echo \CJSON::encode($this->riskIdsForTagIds(explode(",", $tag_ids)));
    }

    public function actionForSets($set_ids)
    {
        echo \CJSON::encode($this->riskIdsForMedicationSetIds(explode(",", $set_ids)));
    }

    /**
     * @param array $medication_set_ids
     * @return array
     */

    protected function riskIdsForMedicationSetIds($medication_set_ids = array())
    {
        return array_map(
            function ($r) {
                return $r->getAttributes();
            },
            OphCiExaminationRisk::findForMedicationSetIds($medication_set_ids)
        );
    }

    public function actionForRefMedication($id)
    {
        if (!$medication =\Medication::model()->findByPk($id)) {
            throw new \CHttpException('Medication not found', 404);
        }

        /** @var \Medication $medication */
        $med_set_ids = array_map(function ($e) {
            return $e->id;
        }, $medication->medicationSets);

        echo \CJSON::encode($this->riskIdsForMedicationSetIds($med_set_ids));
    }

    /**
     * @param $obj
     * @return array
     *
     * @deprecated
     */

    protected function tagIdsForTagged($obj)
    {
        return array_map(
            function ($tag) {
                return $tag->id;
            },
            $obj->tags
        );
    }

    /**
     * @param $ids
     *
     * @deprecated
     */

    public function actionForMedicationDrugIds($ids)
    {
        $meds = \MedicationDrug::model()->with('tags')->findAllByPk(explode(",", $ids));

        $result = array();
        foreach ($meds as $med) {
            $result[$med->id] = $this->riskIdsForTagIds(
                $this->tagIdsForTagged($med)
            );
        }

        echo \CJSON::encode($result);
    }
}
