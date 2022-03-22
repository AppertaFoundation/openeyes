<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php if (Yii::app()->hasModule('Genetics') && Yii::app()->user->checkAccess('OprnViewGeneticPatient')) { ?>
    <div class="subtitle">Genetics:</div>
    <table class="genetics last-left">
        <tbody>
            <?php $subject = GeneticsPatient::model()->findByAttributes(array('patient_id' => $patient->id));?>
            <?php if ($subject) { ?>
                <tr>
                    <th>
                        Genetic database ID:
                    </th>
                    <td>
                        <div class="flex-1">
                            <?php echo $subject->id; ?>
                            ( <?=\CHtml::link('View', Yii::app()->createUrl('/Genetics/subject/view/' . $subject->id)) ?> )
                        </div>
                    </td>
                </tr>
                    <?php if ($subject->pedigrees) {
                        foreach ($subject->pedigrees as $pedigree) {  ?>
                        <tr>
                            <th>
                                Pedigree ID:
                            </th>
                            <td>
                                <div class="flex-1">
                                    <?php echo $pedigree->id ?>
                                    <?php if ($pedigree->gene) {
                                        echo '(Gene: ' . $pedigree->gene->name . ')';
                                    } ?>
                                    <?php
                                    $status = GeneticsPatientPedigree::model()->findByAttributes(array('patient_id' => $subject->id, 'pedigree_id' => $pedigree->id));
                                    if ($status->status !== null) {
                                        echo $status->status->name;
                                    }
                                    ?>
                                    ( <?=\CHtml::link('View', Yii::app()->createUrl('/Genetics/pedigree/view/' . $pedigree->id)); ?> )
                                </div>
                            </td>
                        </tr>
                        <?php }
                    } ?>
                <tr>
                    <th>
                        Diagnosis:
                    </th>
                    <td>
                        <div class="flex-1">
                            <?php
                            if ($subject->diagnoses !== null) {
                                foreach ($subject->diagnoses as $diagnose) {
                                    echo $diagnose->term . "<br>";
                                }
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?php echo $subject->getAttributeLabel('comments') ?>:
                    </th>
                    <td>
                        <div class="flex-1"><?php echo Yii::app()->format->ntext($subject->comments) ?></div>
                    </td>
                </tr>
            <?php } else { ?>
                <?php if (Yii::app()->user->checkAccess('OprnEditGeneticPatient')) { ?>
                <tr>
                    <th>
                        <?=\CHtml::link('Assign Patient to Genetics', Yii::app()->createUrl('Genetics/subject/edit?patient=' . $patient->id)) ?>
                    </th>
                </tr>
                <?php } ?>
            <?php } ?>
            <?php
            $api = Yii::app()->moduleAPI->get('OphInDnasample');
            if ($api) {
                $events = $api->getEventsByPatient($patient);
                if ($events) { ?>
                    <tr>
                        <th>
                            DNA Sample Events:
                        </th>
                        <td>
                            <div class="flex-1">
                                <?php foreach ($events as $event) {
                                    echo EventNavigation::SmallIcon($event, '( View )');
                                    echo ' ' . Helper::convertMySQL2NHS($event->event_date);
                                    ?>
                                    <br/>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            }
            $api = Yii::app()->moduleAPI->get('OphInDnaextraction');
            if ($api) {
                $events = $api->getEventsByPatient($patient);
                if ($events) { ?>
                    <tr>
                        <th>
                            DNA Extraction Events:
                        </th>
                        <td>
                            <div class="flex-1">
                                <?php foreach ($events as $event) {
                                    echo EventNavigation::SmallIcon($event, '( View )');
                                    echo ' ' . Helper::convertMySQL2NHS($event->event_date);
                                    ?>
                                    <br/>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            } ?>
            <?php
            $api = Yii::app()->moduleAPI->get('OphInGeneticresults');
            if ($api) {
                $events = $api->getEventsByPatient($patient);
                if ($events) { ?>
                    <tr>
                        <th>
                            Genetic Result Events:
                        </th>
                        <td>
                            <div class="flex-1">
                                <?php foreach ($events as $event) {
                                    echo EventNavigation::SmallIcon($event, '( View )');
                                    echo ' ' . Helper::convertMySQL2NHS($event->event_date);
                                    ?>
                                    <br/>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            } ?>
        </tbody>
    </table>
<?php } ?>
