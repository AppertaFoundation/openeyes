<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
    <div class="admin box">
    <div class="data-group">
        <div class="cols-10 column"><h2>View Genetics Study</h2></div>
        <div class="cols-2 column right">
            <?php if ($this->checkAccess('OprnEditGeneticPatient')) : ?>
                <a href="/Genetics/study/edit/<?php echo $model->id; ?>?returnUri=<?php echo urlencode('/Genetics/study/view/') . $model->id; ?>" class="button small right" id="study_edit">Edit</a>
            <?php endif; ?>
        </div>
    </div>
        <?php $this->widget('zii.widgets.CDetailView', array(
            'data' => $model,
            'htmlOptions' => array('class' => 'standard flex-layout cols-full'),
            'attributes' => array(
                'name',
                'criteria',
                'end_date',
                array(
                    'label' => 'Subjects',
                    'type' => 'raw',
                    'value' => function () use ($model) {
                        $html = null;
                        if ($model->subjects) {
                            $html = '<ul>';
                            foreach ($model->subjects as $subject) {
                                $html .= '<li>';
                                $html .= '<a href="/Genetics/subject/view/' . $subject->id . '">' . $subject->patient->fullName .  '</a>';
                                $html .= '</li>';
                            }
                            $html .= '</ul>';
                        }
                        return $html;
                    }
                ),
                array(
                    'label' => 'Investigators',
                    'type' => 'raw',
                    'value' => function () use ($model) {
                        $investigators = '';
                        if ($model->proposers) {
                            $investigators = '<ul>';
                            foreach ($model->proposers as $proposer) {
                                $investigators .= '<li>' . $proposer->first_name . ' ' . $proposer->last_name . '</li>';
                            }
                            $investigators .= '</ul>';
                        }
                        return $investigators;
                    }
                )
            )

            ));
