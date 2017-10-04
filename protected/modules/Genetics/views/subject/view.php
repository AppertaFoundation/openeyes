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
    <div class="row">
        <div class="large-10 column"><h2>View Genetics Patient</h2></div>
        <div class="large-2 column right">
            <?php if( $this->checkAccess('OprnEditGeneticPatient') ): ?>
                <a href="/Genetics/subject/edit/<?php echo $model->id . '?patient=' . $model->patient_id; ?>&returnUri=<?php echo urlencode('/Genetics/subject/view/').$model->id; ?>" class="button small right" id="subject_edit">Edit</a>
            <?php endif; ?>
        </div>
    </div>
    <?php $this->widget('zii.widgets.CDetailView', array(
        'data'=>$model,
        'htmlOptions' => array('class'=>'detailview'),
        'attributes'=>array(
            'id',
            array(
                'label' => 'Name',
                'type' => 'raw',
                'value' => function() use ($model){
                    return CHTML::link($model->patient->getFullName(), '/patient/view/' . $model->patient->id);
                }
            ),
            array(
                'label' => 'Maiden Name',
                'type' => 'raw',
                'value' => function() use ($model){
                    return $model->patient->contact->maiden_name;
                }
            ),

            array(
                'label' => $model->patient->getAttributeLabel('dob'),
                'type' => 'raw',
                'value' => function() use ($model){
                    if($model->patient->dob) {
                        $date = new DateTime($model->patient->dob);
                        return $date->format('d M Y');
                    }
                    return null;
                }
            ),
            'patient.hos_num',
            array(
                'label' => $model->getAttributeLabel('gender_id'),
                'value' => isset($model->gender->name) ? $model->gender->name : 'Not set',
            ),

            array(
                'label' => $model->getAttributeLabel('is_deceased'),
                'value' => ($model->is_deceased ? 'yes' : 'no'),
            ),
            'comments',

            array(
                'label' => 'Relationship',
                'type' => 'raw',
                'value' => function() use ($model){
                    $html = '<ul>';
                    foreach($model->relationships as $relationship){
                        $html .= '<li>';
                        $html .= '<a href="/patient/view/' . $relationship->relation->patient->id . '">'.$relationship->relation->patient->fullName.' </a>';
                        $html .= ' is a ' . $relationship->relationship->relationship . ' to the patient.';
                        $html .= '</li>';
                    }
                    return $html .= '<ul>';                    
                }
            ),
             array(
                'label' => $model->getAttributeLabel('diagnoses'),
                'type' => 'raw',
                'value' => function() use ($model){
                    $html = '<ul>';
                    foreach($model->diagnoses as $diagnosis){
                        $html .= '<li>' . $diagnosis->term;
                        $html .= '</li>';
                    }
                    $html .= '</ul>';
                    return $html;
                },
            ),
            array(
                'label' => 'Pedigree',
                'type' => 'raw',
                'value' => function() use ($model){
                    if($model->pedigrees){
                        $html = '<ul>';
                        foreach($model->pedigrees as $pedigree){
                            $gene = isset($pedigree->gene) ? ' (Gene: ' . $pedigree->gene->name . ')' : '';
                            $html .= '<li><a href="/Genetics/pedigree/view/' . $pedigree->id . '">' . $pedigree->id . $gene . '</a>';
                            $html .= " - " . $model->statusForPedigree($pedigree->id);
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                    } else {
                        $html = 'None';
                    }
                    return $html;
                }
            ),
            array(
                'label' => 'Previous Studies',
                'type' => 'raw',
                'value' => function() use ($model){
                    if($model->previous_studies){
                        $html = '<ul>';
                        foreach($model->previous_studies as $previous_study){

                            $end_date = '-';
                            if( Helper::isValidDateTime($previous_study->end_date) ){
                                $end_date_object = new DateTime($previous_study->end_date);
                                $end_date = $end_date_object->format(Helper::NHS_DATE_FORMAT);
                            }

                            $html .= '<li>';
                            $html .= $previous_study->name . ' - ' . '<i>End date: ' . $end_date . '</i>';
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                    } else {
                        return $html = 'None';
                    }
                    return $html;
                }
            ),
            
            array(
                'label' => 'Rejected Studies',
                'type' => 'raw',
                'value' => function() use ($model){
                    if($model->rejected_studies){
                        $html = '<ul>';
                        foreach($model->rejected_studies as $rejected_study){
                            $html .= '<li>';
                            $html .= $rejected_study->name;
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                    } else {
                        $html = 'None';
                    }
                    return $html;
                }
            ),
            
            array(
                'label' => 'Current Studies',
                'type' => 'raw',
                'value' => function() use ($model){
                    if($model->current_studies){
                        $html = '<ul>';
                        foreach($model->current_studies as $current_study){
                            $html .= '<li>';
                            $html .= $current_study->name;
                            $html .= '</li>';
                        }
                         $html .= '</ul>';
                    } else {
                        $html = 'None';
                    }
                    return $html;
                }
                    
            ),
    ),
)); ?>
    </div>