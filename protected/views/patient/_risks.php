<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<section class="element view full patient-info associated-data js-toggle-container">
    <header class="element-header">
        <h3 class="element-title">
            <span class="icon-patient-clinician-hd_flag"></span>
            Risks
        </h3>
    </header>
    <div class="jelement-data full-width js-toggle-body">
        <?php $this->widget('OEModule\OphCiExamination\widgets\HistoryRisks', array(
            'patient' => $this->patient,
            'mode' => BaseEventElementWidget::$PATIENT_SUMMARY_MODE
        )); ?>
    </div>
</section>