<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\models;

class OphCiExamination_Episode_IOP1 extends \EpisodeSummaryWidget
{
    public function run()
    {

        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
        if ($iop = $exam_api->getElements(
            'OEModule\OphCiExamination\models\Element_OphCiExamination_IntraocularPressure',
            $this->episode->patient,
            false
        )
        ) {
            $this->render('OphCiExamination_Episode_IOP', array('iop' => end($iop)));
        }
    }
}
