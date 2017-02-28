<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
    <div class="admin box">
    <div class="row">
        <div class="large-10 column"><h2>View Subject</h2></div>
        <div class="large-2 column right">
            <?php if( $this->checkAccess('OprnEditGeneticPatient') ): ?>
                <a href="/Genetics/subject/edit/<?php echo $model->id; ?>" class="button small right" id="subject_edit">Edit</a>
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
                'label' => $model->patient->getAttributeLabel('dob'),
                'type' => 'raw',
                'value' => function() use ($model){
                    $date = new DateTime($model->patient->dob);
                    return $date->format('d M Y');
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
                    $html = '<ul>';
                    foreach($model->pedigrees as $pedigree){
                        $gene = isset($pedigree->gene) ? ' (Gene: ' . $pedigree->gene->name . ')' : '';
                        $html .= '<li><a href="/Genetics/pedigree/view/' . $pedigree->id . '">' . $pedigree->id . $gene . '</a>';
                        foreach($pedigree->members as $member){
                            $html .= " - " . $member->status->name;
                        }
                        $html .= '</li>';
                    }
                    return $html .= '</ul>';
                }
            ),
            array(
                'label' => 'Previous Studies',
                'type' => 'raw',
                'value' => function() use ($model){
                    $html = '<ul>';
                    foreach($model->previous_studies as $previous_study){
                        $end_data = new DateTime($previous_study->end_date);
                        $html .= '<li>';
                        $html .= $previous_study->name . ' - ' . '<i>Ended: ' . $end_data->format('d M Y') . '</i>';
                        $html .= '</li>';
                    }
                    return $html .= '</ul>';
                }
            ),
            
            array(
                'label' => 'Rejected Studies',
                'type' => 'raw',
                'value' => function() use ($model){
                    $html = '<ul>';
                    foreach($model->rejected_studies as $rejected_study){
                        $html .= '<li>';
                        $html .= $rejected_study->name;
                        $html .= '</li>';
                    }
                    return $html .= '</ul>';
                }
            ),
            
            array(
                'label' => 'Current Studies',
                'type' => 'raw',
                'value' => function() use ($model){
                    $html = '<ul>';
                    foreach($model->current_studies as $current_study){
                        $html .= '<li>';
                        $html .= $current_study->name;
                        $html .= '</li>';
                    }
                    return $html .= '</ul>';
                }
            ),
            
            
            
     /*       'disorder',
            array(
                'label' => $model->getAttributeLabel('consanguinity'),
                'value' => $model->consanguinity ? 'yes' : 'no',
            ),
            'gene.name',
            'base_change_type',
            array(
                'label' => $model->getAttributeLabel('base_change'),
                'value' => $model->base_change ? $model->base_change : '<span class="null">Not set</span>',
                'type'=>'raw',
            ),
            'aminod_acid_change_type',
            array(
                'label' => $model->getAttributeLabel('amino_acid_change'),
                'value' => $model->amino_acid_change ? $model->amino_acid_change : '<span class="null">Not set</span>',
                'type' => 'raw',
            ),
            array(
                'label' => $model->getAttributeLabel('genomic_coordinate'),
                'type' => 'raw',
                'value' => $model->genomic_coordinate ? $model->genomic_coordinate : '<span class="null">Not set</span>',
            ),
            array(
                'label' => $model->getAttributeLabel('genome_version'),
                'type' => 'raw',
                'value' => $model->genome_version ? $model->genome_version : '<span class="null">Not set</span>',
            ),
            array(
                'label' => $model->getAttributeLabel('gene_transcript'),
                'type' => 'raw',
                'value' => $model->gene_transcript ? $model->gene_transcript : '<span class="null">Not set</span>',
            ),
            array(
                'label' => $model->getAttributeLabel('created_date'),
                'type' => 'raw',
                'value' => function() use ($model){
                    $date = new DateTime($model->created_date);
                    return $date->format('d M Y');
                }
            ),
            array(
                'label' => 'Subjects',
                'type' => 'raw',
                'value' => function() use ($model){

                    $html = '<ul class="subjects_list">';
                    foreach($model->subjects as $subject){
                        $html .= '<li>';
                        $html .= '<a href="/Genetics/subject/view/' . $subject->id . '" title="' . $subject->patient->fullName . '">';
                        $html .= $subject->patient->fullName . '</a>';
                        $html .= '<span class="status"><i>(Status: ' . $subject->statusForPedigree($model->id) . ')</i></span>';
                        $html .= '</li>';
                    }
                    $html .= '</ul>';
                    return $html;

                }
            )*/
    ),
)); ?>
    </div>